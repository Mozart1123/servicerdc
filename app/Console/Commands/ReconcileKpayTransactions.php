<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\KpayService;
use Illuminate\Support\Facades\Log;

class ReconcileKpayTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kpay:reconcile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile pending K-PAY transactions by polling the API';

    /**
     * Execute the console command.
     */
    public function handle(KpayService $kpayService)
    {
        $this->info('Starting K-PAY transaction reconciliation...');

        // Find transactions pending for more than 5 minutes
        $transactions = Transaction::where('status', 'pending')
            ->whereNotNull('kpay_reference')
            ->where('created_at', '<', now()->subMinutes(5))
            ->get();

        if ($transactions->isEmpty()) {
            $this->info('No pending transactions to reconcile.');
            return;
        }

        foreach ($transactions as $transaction) {
            try {
                $response = $kpayService->getTransactionStatus($transaction->kpay_reference);
                
                $status = $response['status'] ?? null;
                
                if ($status === 'COMPLETED') {
                    $transaction->status = 'succeeded';
                    // We also need to fulfill the order since the webhook was missed
                    $this->fulfillTransaction($transaction);
                    $transaction->save();
                    $this->info("Transaction {$transaction->reference_id} marked as succeeded.");
                } elseif ($status === 'FAILED' || $status === 'CANCELLED') {
                    $transaction->status = 'failed';
                    $transaction->save();
                    $this->info("Transaction {$transaction->reference_id} marked as failed.");
                } else {
                    $this->info("Transaction {$transaction->reference_id} is still pending.");
                }

            } catch (\Exception $e) {
                Log::error("Failed to reconcile transaction {$transaction->reference_id}: " . $e->getMessage());
                $this->error("Error checking {$transaction->reference_id}: " . $e->getMessage());
            }
        }

        $this->info('Reconciliation complete.');
    }

    /**
     * Manually trigger the fulfillment logic usually done by the webhook.
     */
    protected function fulfillTransaction(Transaction $transaction)
    {
        if ($transaction->type === 'subscription') {
            // Same lookup as KpayWebhookController::fulfillSubscription() —
            // find the exact row this payment was initiated for via
            // transaction_ref first, to avoid matching an old
            // cancelled/expired subscription to the same plan.
            $subscription = Subscription::where('transaction_ref', $transaction->reference_id)->first()
                ?? Subscription::firstOrNew([
                    'user_id' => $transaction->user_id,
                    'subscription_plan_id' => $transaction->item_id,
                ]);

            // Must be read BEFORE the status is set below, otherwise it
            // always evaluates true and a brand-new subscription's null
            // ends_at crashes the ->copy() call further down.
            $wasActive = $subscription->isActive();

            $subscription->status = 'active';
            $subscription->paid_at = now();
            $subscription->amount_paid = $transaction->amount;
            $subscription->transaction_ref = $transaction->kpay_reference ?? $transaction->reference_id;

            $startsAt = ($wasActive && $subscription->ends_at) ? $subscription->ends_at : now();
            $subscription->starts_at = $startsAt;
            $duration = $subscription->billing_cycle === 'yearly' ? 12 : 1;
            $subscription->ends_at = $startsAt->copy()->addMonths($duration);
            $subscription->save();

            $planName = $subscription->subscriptionPlan->name ?? 'Abonnement';

            Notification::create([
                'user_id'      => $transaction->user_id,
                'type'         => 'subscription_activated',
                'related_type' => 'subscription',
                'related_id'   => $subscription->id,
                'title'        => 'Paiement confirmé — Abonnement activé !',
                'message'      => "Votre paiement de {$transaction->amount} {$transaction->currency} a été confirmé. Votre abonnement {$planName} est maintenant actif.",
                'data'         => [
                    'transaction_ref' => $transaction->reference_id,
                    'amount'          => $transaction->amount,
                    'currency'        => $transaction->currency,
                    'plan_id'         => $subscription->subscription_plan_id,
                    'plan_name'       => $planName,
                ],
                'is_read'      => false,
                'action_url'   => '/user/subscription',
            ]);

            $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id'      => $admin->id,
                    'type'         => 'subscription_paid',
                    'related_type' => 'subscription',
                    'related_id'   => $subscription->id,
                    'title'        => 'Nouvel abonnement payé',
                    'message'      => "{$transaction->user->name} a payé son abonnement {$planName} ({$transaction->amount} {$transaction->currency}).",
                    'data'         => [
                        'transaction_ref' => $transaction->reference_id,
                        'amount'          => $transaction->amount,
                        'currency'        => $transaction->currency,
                        'plan_id'         => $subscription->subscription_plan_id,
                        'plan_name'       => $planName,
                    ],
                    'is_read'      => false,
                    'action_url'   => '/admin/finances/transactions',
                ]);
            }
        } elseif ($transaction->type === 'mission') {
            $mission = \App\Models\Mission::find($transaction->item_id);
            if ($mission) {
                Log::info("Mission #{$mission->id} payment reconciled successfully.");
            }
        }
    }
}
