<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\KpayService;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\Payout;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class KpayWebhookController extends Controller
{
    protected $kpayService;

    public function __construct(KpayService $kpayService)
    {
        $this->kpayService = $kpayService;
    }

    /**
     * Handle Generic Webhook Events
     */
    public function handleGeneric(Request $request)
    {
        return $this->processWebhook($request);
    }

    /**
     * Handle Deposit Events
     */
    public function handleDeposit(Request $request)
    {
        return $this->processWebhook($request);
    }

    /**
     * Handle Payout Events
     */
    public function handlePayout(Request $request)
    {
        return $this->processWebhook($request);
    }

    /**
     * Handle Refund Events
     */
    public function handleRefund(Request $request)
    {
        return $this->processWebhook($request);
    }

    /**
     * Core webhook processor
     */
    protected function processWebhook(Request $request)
    {
        // 1. Verify Signature
        $signature = $request->header('X-KPAY-Signature');
        $payload = $request->getContent(); // Get raw body
        
        if (!$this->kpayService->validateWebhookSignature($payload, $signature)) {
            Log::warning('K-PAY Webhook Signature Validation Failed', [
                'ip' => $request->ip(),
                'signature' => $signature
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->json()->all();
        $event = $data['event'] ?? 'unknown';
        $kpayReference = $data['paymentId'] ?? null;
        $externalId = $data['externalId'] ?? null; // Our internal reference id
        $status = $data['status'] ?? null;

        Log::info("K-PAY Webhook Received: {$event}", ['data' => $data]);

        if (!$externalId) {
            return response()->json(['message' => 'No externalId provided'], 200);
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($externalId, $status, $kpayReference, $event, $data) {
                
                // ─── 1. HANDLE WITHDRAWALS (ADMIN) ───
                $withdrawal = Withdrawal::where('reference_id', $externalId)->lockForUpdate()->first();
                if ($withdrawal) {
                    if (in_array($withdrawal->status, ['completed', 'failed', 'cancelled'])) {
                        return; // Already processed
                    }
                    
                    if (!$withdrawal->kpay_reference && $kpayReference) {
                        $withdrawal->kpay_reference = $kpayReference;
                    }
                    
                    if ($status === 'COMPLETED') {
                        $withdrawal->status = 'completed';
                    } elseif ($status === 'FAILED' || $status === 'CANCELLED') {
                        $withdrawal->status = 'failed';
                    }
                    
                    $withdrawal->save();
                    return;
                }
                
                // ─── 2. HANDLE PAYOUTS (ORGANIZATIONS) ───
                $payout = Payout::where('reference_id', $externalId)->lockForUpdate()->first();
                if ($payout) {
                    if (in_array($payout->status, ['paid', 'failed', 'canceled'])) {
                        return;
                    }
                    if ($status === 'COMPLETED') {
                        $payout->status = 'paid';
                        $payout->paid_at = now();
                    } elseif ($status === 'FAILED' || $status === 'CANCELLED') {
                        $payout->status = 'failed';
                    }
                    $payout->save();
                    return;
                }

                // ─── 3. HANDLE TRANSACTIONS (DEPOSITS / PAYMENTS) ───
                // Lock the transaction row to prevent race conditions
                $transaction = Transaction::where('reference_id', $externalId)->lockForUpdate()->first();

                if (!$transaction) {
                    Log::warning('K-PAY Webhook Entity Not Found', ['externalId' => $externalId]);
                    return; // Will exit transaction block
                }

                // If already succeeded, nothing to do
                if ($transaction->status === 'succeeded') {
                    return; 
                }

                // Store K-Pay's internal ID if not already saved
                if ($kpayReference && !$transaction->kpay_reference) {
                    $transaction->kpay_reference = $kpayReference;
                }

                // Update transaction status based on K-Pay status
                if ($status === 'COMPLETED') {
                    $transaction->status = 'succeeded';
                    
                    // Fulfillment Logic
                    if ($transaction->type === 'subscription') {
                        $subscription = \App\Models\Subscription::firstOrNew([
                            'user_id' => $transaction->user_id,
                            'subscription_plan_id' => $transaction->item_id,
                        ]);
                        
                        $subscription->status = 'active';
                        $subscription->paid_at = now();
                        $subscription->amount_paid = $transaction->amount;
                        $subscription->transaction_ref = $kpayReference ?? $transaction->reference_id;
                        
                        $startsAt = $subscription->isActive() ? $subscription->ends_at : now();
                        $subscription->starts_at = $startsAt;
                        
                        // Check duration from billing_cycle if available
                        $duration = $subscription->billing_cycle === 'yearly' ? 12 : 1;
                        $subscription->ends_at = $startsAt->copy()->addMonths($duration);
                        $subscription->save();
                        
                        // Generate Admin Notification
                        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
                        foreach ($admins as $admin) {
                            Notification::create([
                                'user_id' => $admin->id,
                                'type' => 'subscription_paid',
                                'related_type' => 'subscription',
                                'related_id' => $subscription->id,
                                'title' => 'Nouvel abonnement payé',
                                'message' => "Le client {$transaction->user->name} a payé son abonnement ({$transaction->amount} {$transaction->currency}).",
                                'data' => [
                                    'transaction_ref' => $transaction->reference_id,
                                    'amount' => $transaction->amount,
                                    'currency' => $transaction->currency,
                                    'plan_id' => $transaction->item_id
                                ],
                                'is_read' => false,
                                'action_url' => '/admin/finances/transactions',
                            ]);
                        }
                    } elseif ($transaction->type === 'mission') {
                        // Mark mission as paid or trigger notifications here
                        $mission = \App\Models\Mission::find($transaction->item_id);
                        if ($mission) {
                            // Example: $mission->status = 'in_progress'; $mission->save();
                            Log::info("Mission #{$mission->id} payment confirmed via webhook.");
                        }
                    }

                } elseif ($status === 'FAILED' || $status === 'CANCELLED') {
                    $transaction->status = 'failed';
                }

                $transaction->save();
            });
        } catch (\Exception $e) {
            Log::error('K-PAY Webhook Processing Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }

        // Send 200 OK fast
        return response()->json(['message' => 'Webhook processed successfully'], 200);
    }
}
