<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
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
            $subscription = \App\Models\Subscription::firstOrNew([
                'user_id' => $transaction->user_id,
                'subscription_plan_id' => $transaction->item_id,
            ]);
            
            $subscription->status = 'active';
            $subscription->paid_at = now();
            $subscription->amount_paid = $transaction->amount;
            $subscription->transaction_ref = $transaction->kpay_reference ?? $transaction->reference_id;
            
            $startsAt = $subscription->isActive() ? $subscription->ends_at : now();
            $subscription->starts_at = $startsAt;
            $subscription->ends_at = $startsAt->copy()->addMonth();
            $subscription->save();
        } elseif ($transaction->type === 'mission') {
            $mission = \App\Models\Mission::find($transaction->item_id);
            if ($mission) {
                Log::info("Mission #{$mission->id} payment reconciled successfully.");
            }
        }
    }
}
