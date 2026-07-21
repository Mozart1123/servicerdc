<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\KpayService;
use App\Models\Transaction;
use App\Models\KpayTransaction;
use App\Models\Withdrawal;
use App\Models\Payout;
use App\Models\Mission;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
     *
     * Security:   Validates HMAC-SHA256 signature on X-KPAY-Signature header.
     * Idempotency: Checks kpay_transactions.transaction_ref before processing.
     * Audit:       Creates/updates a KpayTransaction record with raw response.
     */
    protected function processWebhook(Request $request)
    {
        // ── 1. SIGNATURE VERIFICATION ─────────────────────────────────────────
        $signature = $request->header('X-KPAY-Signature');
        $payload   = $request->getContent(); // raw body required for HMAC

        if (!$this->kpayService->validateWebhookSignature($payload, $signature)) {
            Log::warning('K-PAY Webhook: invalid signature rejected', [
                'ip'        => $request->ip(),
                'signature' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data        = $request->json()->all();
        $event       = $data['event']      ?? 'unknown';
        $kpayRef     = $data['paymentId']  ?? null;      // K-PAY internal ID
        $externalId  = $data['externalId'] ?? null;      // our reference_id
        $status      = $data['status']     ?? null;

        Log::info("K-PAY Webhook received: {$event}", ['externalId' => $externalId, 'status' => $status]);

        if (!$externalId) {
            return response()->json(['message' => 'No externalId provided'], 200);
        }

        try {
            DB::transaction(function () use ($externalId, $status, $kpayRef, $event, $data) {

                // ── 2. HANDLE WITHDRAWALS (ADMIN) ──────────────────────────────
                $withdrawal = Withdrawal::where('reference_id', $externalId)->lockForUpdate()->first();
                if ($withdrawal) {
                    if (in_array($withdrawal->status, ['completed', 'failed', 'cancelled'])) {
                        return; // Already processed — idempotent
                    }
                    if (!$withdrawal->kpay_reference && $kpayRef) {
                        $withdrawal->kpay_reference = $kpayRef;
                    }
                    $withdrawal->status = match (true) {
                        $status === 'COMPLETED'                       => 'completed',
                        in_array($status, ['FAILED', 'CANCELLED'])    => 'failed',
                        default                                        => $withdrawal->status,
                    };
                    $withdrawal->save();
                    return;
                }

                // ── 3. HANDLE PAYOUTS (ORGANIZATIONS) ─────────────────────────
                $payout = Payout::where('reference_id', $externalId)->lockForUpdate()->first();
                if ($payout) {
                    if (in_array($payout->status, ['paid', 'failed', 'canceled'])) {
                        return; // Already processed — idempotent
                    }
                    if ($status === 'COMPLETED') {
                        $payout->status  = 'paid';
                        $payout->paid_at = now();
                    } elseif (in_array($status, ['FAILED', 'CANCELLED'])) {
                        $payout->status = 'failed';
                    }
                    $payout->save();
                    return;
                }

                // ── 4. HANDLE TRANSACTIONS (DEPOSITS / MISSION PAYMENTS) ───────
                $transaction = Transaction::where('reference_id', $externalId)->lockForUpdate()->first();

                if (!$transaction) {
                    Log::warning('K-PAY Webhook: no matching transaction found', ['externalId' => $externalId]);
                    return;
                }

                // ── 4a. IDEMPOTENCY — check kpay_transactions audit table ──────
                // If a KpayTransaction for this ref already reached terminal state, silently ignore.
                $kpayTx = KpayTransaction::where('transaction_ref', $externalId)->lockForUpdate()->first();

                if ($kpayTx && in_array($kpayTx->status, ['success', 'failed'])) {
                    Log::info('K-PAY Webhook: duplicate webhook ignored (already processed)', [
                        'transaction_ref' => $externalId,
                        'status'          => $kpayTx->status,
                    ]);
                    return; // Silently ignore — idempotent
                }

                // ── 4b. Update the generic Transaction record ──────────────────
                if ($kpayRef && !$transaction->kpay_reference) {
                    $transaction->kpay_reference = $kpayRef;
                }

                $newStatus = match (true) {
                    $status === 'COMPLETED'                    => 'succeeded',
                    in_array($status, ['FAILED', 'CANCELLED']) => 'failed',
                    default                                     => $transaction->status,
                };
                $transaction->status = $newStatus;
                $transaction->save();

                // ── 4c. Update or create KpayTransaction audit record ──────────
                $auditStatus = match ($newStatus) {
                    'succeeded' => 'success',
                    'failed'    => 'failed',
                    default     => 'pending',
                };

                if ($kpayTx) {
                    $kpayTx->status       = $auditStatus;
                    $kpayTx->raw_response = $data;
                    $kpayTx->save();
                } else {
                    // Create audit record if not yet created (e.g. webhook arrived before initiation record)
                    KpayTransaction::create([
                        'mission_id'      => $transaction->mission_id,
                        'transaction_ref' => $externalId,
                        'phone_number'    => $data['phoneNumber'] ?? 'unknown',
                        'amount'          => $transaction->amount,
                        'status'          => $auditStatus,
                        'raw_response'    => $data,
                    ]);
                }

                // ── 4d. FULFILLMENT LOGIC ──────────────────────────────────────
                if ($newStatus === 'succeeded') {

                    if ($transaction->type === 'subscription') {
                        $this->fulfillSubscription($transaction, $kpayRef);
                    } elseif ($transaction->type === 'mission') {
                        $this->fulfillMissionPayment($transaction, $kpayRef);
                    }

                } elseif ($newStatus === 'failed') {
                    $this->handleMissionPaymentFailure($transaction);
                }
            });

        } catch (\Exception $e) {
            Log::error('K-PAY Webhook processing error: ' . $e->getMessage(), [
                'externalId' => $externalId,
                'trace'      => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }

        return response()->json(['message' => 'Webhook processed successfully'], 200);
    }

    // ============================================================
    // FULFILLMENT HELPERS
    // ============================================================

    /**
     * Fulfill a subscription payment.
     */
    protected function fulfillSubscription(Transaction $transaction, ?string $kpayRef): void
    {
        $subscription = \App\Models\Subscription::firstOrNew([
            'user_id'              => $transaction->user_id,
            'subscription_plan_id' => $transaction->item_id,
        ]);

        $subscription->status          = 'active';
        $subscription->paid_at         = now();
        $subscription->amount_paid     = $transaction->amount;
        $subscription->transaction_ref = $kpayRef ?? $transaction->reference_id;

        $startsAt                  = $subscription->isActive() ? $subscription->ends_at : now();
        $subscription->starts_at   = $startsAt;
        $duration                  = $subscription->billing_cycle === 'yearly' ? 12 : 1;
        $subscription->ends_at     = $startsAt->copy()->addMonths($duration);
        $subscription->save();

        // Notify admins
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'      => $admin->id,
                'type'         => 'subscription_paid',
                'related_type' => 'subscription',
                'related_id'   => $subscription->id,
                'title'        => 'Nouvel abonnement payé',
                'message'      => "Le client {$transaction->user->name} a payé son abonnement ({$transaction->amount} {$transaction->currency}).",
                'data'         => [
                    'transaction_ref' => $transaction->reference_id,
                    'amount'          => $transaction->amount,
                    'currency'        => $transaction->currency,
                    'plan_id'         => $transaction->item_id,
                ],
                'is_read'      => false,
                'action_url'   => '/admin/finances/transactions',
            ]);
        }
    }

    /**
     * Fulfill a mission commission payment:
     * - Sets commission_status → 'paid'
     * - Sets payout_status    → 'pending_payout' (artisan is now owed money)
     * - Notifies client, artisan and admins
     */
    protected function fulfillMissionPayment(Transaction $transaction, ?string $kpayRef): void
    {
        $mission = Mission::find($transaction->mission_id ?? $transaction->item_id);

        if (!$mission) {
            Log::warning('K-PAY Webhook: fulfillMissionPayment – mission not found', [
                'item_id'    => $transaction->item_id,
                'mission_id' => $transaction->mission_id,
            ]);
            return;
        }

        // Only update if not already paid (extra safety net)
        if ($mission->commission_status === 'paid') {
            Log::info("K-PAY Webhook: mission #{$mission->id} commission already marked as paid.");
            return;
        }

        $mission->commission_status = 'paid';
        $mission->payout_status     = 'pending_payout'; // artisan payout is now owed
        $mission->contact_unlocked_at = now();
        $mission->save();

        // ── SYNC WITH SERVICEREQUEST ──
        $serviceRequest = $mission->serviceRequest;
        if ($serviceRequest) {
            if ($serviceRequest->status === 'cancelled' || $serviceRequest->status === 'rejected') {
                // Payment succeeded but request was cancelled -> Potential refund needed
                Log::alert("K-PAY Webhook: mission #{$mission->id} paid BUT ServiceRequest #{$serviceRequest->id} is {$serviceRequest->status}. Manual refund may be required.");
                
                $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id'    => $admin->id,
                        'type'       => 'refund_needed',
                        'title'      => 'Alerte: Paiement sur demande annulée',
                        'message'    => "Paiement reçu pour la Mission #{$mission->id} alors que la demande liée est annulée/rejetée. Un remboursement peut être nécessaire.",
                        'action_url' => route('admin.missions.index'),
                        'is_read'    => false,
                    ]);
                }
            } elseif ($serviceRequest->status === 'accepted') {
                $serviceRequest->update([
                    'status'      => 'in_progress',
                    'accepted_at' => now(), // Starts the chrono on the UI
                ]);
                $mission->update([
                    'status'     => 'in_progress',
                    'start_date' => now(),
                ]);
            }
        } elseif ($mission->status === 'pending') {
            $mission->status = 'in_progress';
            $mission->save();
        }

        Log::info("K-PAY Webhook: mission #{$mission->id} commission paid. Payout pending for artisan #{$mission->artisan_id}.");

        // Notify client
        if ($mission->client_id) {
            Notification::create([
                'user_id'      => $mission->client_id,
                'type'         => 'mission_payment_confirmed',
                'related_type' => 'mission',
                'related_id'   => $mission->id,
                'title'        => 'Paiement confirmé',
                'message'      => "Votre paiement de commission pour la mission \"{$mission->title}\" a été confirmé.",
                'data'         => [
                    'mission_id'      => $mission->id,
                    'transaction_ref' => $transaction->reference_id,
                    'amount'          => $transaction->amount,
                ],
                'is_read'    => false,
                'action_url' => '/user/missions/' . $mission->id,
            ]);
        }

        // Notify artisan
        if ($mission->artisan_id) {
            Notification::create([
                'user_id'      => $mission->artisan_id,
                'type'         => 'mission_payment_received',
                'related_type' => 'mission',
                'related_id'   => $mission->id,
                'title'        => 'Paiement reçu pour votre mission',
                'message'      => "La commission de la mission \"{$mission->title}\" a été encaissée. Votre paiement est en attente de traitement.",
                'data'         => [
                    'mission_id' => $mission->id,
                    'amount'     => $mission->amount,
                ],
                'is_read'    => false,
                'action_url' => '/user/missions/' . $mission->id,
            ]);
        }

        // Notify admins
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'      => $admin->id,
                'type'         => 'mission_commission_paid',
                'related_type' => 'mission',
                'related_id'   => $mission->id,
                'title'        => 'Commission mission encaissée',
                'message'      => "Mission #{$mission->id} \"{$mission->title}\" : commission de {$transaction->amount} {$transaction->currency} confirmée. Payout artisan en attente.",
                'data'         => [
                    'mission_id'      => $mission->id,
                    'artisan_id'      => $mission->artisan_id,
                    'transaction_ref' => $transaction->reference_id,
                    'amount'          => $transaction->amount,
                ],
                'is_read'    => false,
                'action_url' => '/admin/missions/' . $mission->id,
            ]);
        }
    }

    /**
     * Handle a failed mission payment — notify the client so they can retry.
     */
    protected function handleMissionPaymentFailure(Transaction $transaction): void
    {
        $mission = Mission::find($transaction->mission_id ?? $transaction->item_id);
        if (!$mission || !$mission->client_id) {
            return;
        }

        Notification::create([
            'user_id'      => $mission->client_id,
            'type'         => 'mission_payment_failed',
            'related_type' => 'mission',
            'related_id'   => $mission->id,
            'title'        => 'Échec du paiement',
            'message'      => "Le paiement de commission pour la mission \"{$mission->title}\" a échoué. Veuillez réessayer.",
            'data'         => [
                'mission_id'      => $mission->id,
                'transaction_ref' => $transaction->reference_id,
            ],
            'is_read'    => false,
            'action_url' => '/user/missions/' . $mission->id,
        ]);
    }
}
