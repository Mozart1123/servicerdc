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
use App\Models\ServiceRequest;
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

                // ── 4a-bis. REFUND EVENTS ───────────────────────────────────────
                // Handled separately from the generic COMPLETED/FAILED/CANCELLED
                // mapping below: a refund event arrives for a transaction that is
                // already 'succeeded' and needs its own state transition + wallet
                // reversal, not a re-run of the normal payment-fulfillment logic.
                if (str_starts_with($event, 'refund.')) {
                    $this->handleTransactionRefund($transaction, $status, $kpayRef, $data);
                    return;
                }

                // ── 4b. Update the generic Transaction record ──────────────────
                if ($kpayRef && !$transaction->kpay_reference) {
                    $transaction->kpay_reference = $kpayRef;
                }

                // Audit: surface any mismatch between the amount/currency we
                // initiated with and what K-PAY actually confirms back. This is
                // our main empirical signal for the RD Congo providers
                // (VODACOM_MPESA_COD, AIRTEL_COD, ORANGE_COD), whose docs list
                // both CDF and USD as supported without documenting how a given
                // deposit's currency is actually chosen — if K-PAY is silently
                // charging in a different currency than we assumed, this is what
                // will show it.
                if (isset($data['currency']) && $data['currency'] !== $transaction->currency) {
                    Log::warning('K-PAY Webhook: currency mismatch — K-PAY confirmed a different currency than we assumed', [
                        'externalId'       => $externalId,
                        'assumed_currency' => $transaction->currency,
                        'kpay_currency'    => $data['currency'],
                    ]);
                }
                if (isset($data['amount']) && round((float) $data['amount'], 2) !== round((float) $transaction->amount, 2)) {
                    Log::warning('K-PAY Webhook: amount mismatch between our record and K-PAY confirmation', [
                        'externalId'  => $externalId,
                        'our_amount'  => $transaction->amount,
                        'kpay_amount' => $data['amount'],
                    ]);
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
                    } elseif ($transaction->type === 'service_request') {
                        $this->fulfillServiceRequestPayment($transaction, $kpayRef);
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

        // ── CREDIT ARTISAN WALLET ──
        if ($mission->artisan_id) {
            $artisan = User::find($mission->artisan_id);
            if ($artisan) {
                $wallet = $artisan->getOrCreateWallet();
                $wallet->credit(
                    amount: (float) ($mission->amount ?? $transaction->amount),
                    commissionAmount: (float) ($transaction->amount ?? 0),
                    fromUserId: $mission->client_id,
                    missionId: $mission->id,
                    description: "Paiement reçu pour la mission \"{$mission->title}\"",
                    referenceId: $kpayRef ?? $transaction->reference_id
                );
            }
        }

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

        Log::info("K-PAY Webhook: mission #{$mission->id} commission paid. Wallet credited & payout pending for artisan #{$mission->artisan_id}.");

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

    /**
     * Handle a refund.* webhook event for a transaction that was previously
     * 'succeeded' (whether the refund was triggered from our own admin panel
     * via KpayService::refundPayment(), or directly from K-PAY's dashboard —
     * this is the single source of truth either way).
     *
     * On a completed refund:
     *  - Marks the transaction 'refunded' (idempotent: a second refund.completed
     *    webhook for the same transaction is a no-op).
     *  - Reverses the artisan's wallet credit, if one was made for this payment
     *    (mission / service_request payments only — subscriptions never credit
     *    a wallet, so this step is simply skipped for those).
     *  - Notifies the client, the artisan (if the wallet was touched), and admins.
     *
     * If the wallet reversal itself fails (e.g. the artisan already withdrew
     * the funds and the wallet balance is now too low to debit), the refund
     * to the client already happened at K-PAY — we don't roll that back. We
     * flag it for manual admin follow-up instead, same pattern already used
     * elsewhere in this controller for other "money already moved, needs a
     * human" edge cases.
     */
    protected function handleTransactionRefund(Transaction $transaction, ?string $status, ?string $kpayRef, array $data): void
    {
        // Idempotency — a transaction can only be refunded once.
        if ($transaction->status === 'refunded') {
            Log::info("K-PAY Webhook: refund event ignored — transaction #{$transaction->id} already marked refunded.");
            return;
        }

        if ($status !== 'COMPLETED') {
            // Refund attempt failed or was cancelled on K-PAY's side — the
            // transaction stays 'succeeded' (the original payment is untouched).
            Log::warning("K-PAY Webhook: refund NOT completed for transaction #{$transaction->id}", [
                'status'     => $status,
                'kpayRef'    => $kpayRef,
                'reference'  => $transaction->reference_id,
            ]);

            $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id'    => $admin->id,
                    'type'       => 'refund_failed',
                    'title'      => 'Échec de remboursement K-PAY',
                    'message'    => "Le remboursement du paiement #{$transaction->reference_id} a échoué côté K-PAY (statut: {$status}). Vérifiez manuellement.",
                    'is_read'    => false,
                    'action_url' => '/admin/support-hq/tickets',
                ]);
            }
            return;
        }

        $transaction->status = 'refunded';
        $transaction->save();

        Log::info("K-PAY Webhook: transaction #{$transaction->id} refunded.", ['reference' => $transaction->reference_id]);

        // ── REVERSE ARTISAN WALLET CREDIT, IF ANY ──
        $walletReversed = false;
        $walletReversalFailed = false;
        $lookupRef = $transaction->kpay_reference ?? $transaction->reference_id;

        $creditTx = \App\Models\WalletTransaction::where('reference_id', $lookupRef)
            ->where('type', 'credit')
            ->first();

        if ($creditTx) {
            $wallet = $creditTx->wallet;
            if ($wallet) {
                try {
                    $wallet->debit(
                        amount: $creditTx->net_amount,
                        description: "Remboursement — {$creditTx->description}",
                        referenceId: 'REFUND-' . $lookupRef
                    );
                    $walletReversed = true;
                } catch (\Throwable $e) {
                    // Most likely: artisan already withdrew the funds, balance too low.
                    $walletReversalFailed = true;
                    Log::alert("K-PAY Webhook: could not reverse wallet credit for refunded transaction #{$transaction->id}: " . $e->getMessage());
                }
            }
        }

        // ── NOTIFY CLIENT ──
        if ($transaction->user_id) {
            Notification::create([
                'user_id'      => $transaction->user_id,
                'type'         => 'payment_refunded',
                'related_type' => 'transaction',
                'related_id'   => $transaction->id,
                'title'        => 'Remboursement confirmé',
                'message'      => "Votre paiement de {$transaction->amount} {$transaction->currency} a été remboursé.",
                'is_read'      => false,
            ]);
        }

        // ── NOTIFY ARTISAN, IF THE WALLET WAS TOUCHED ──
        if ($creditTx && $creditTx->user_id) {
            Notification::create([
                'user_id'      => $creditTx->user_id,
                'type'         => $walletReversed ? 'wallet_debited_refund' : 'wallet_refund_needs_review',
                'related_type' => 'transaction',
                'related_id'   => $transaction->id,
                'title'        => $walletReversed ? 'Paiement remboursé au client' : 'Remboursement — action requise',
                'message'      => $walletReversed
                    ? "Le paiement lié à \"{$creditTx->description}\" a été remboursé au client ; le montant correspondant a été retiré de votre solde."
                    : "Le paiement lié à \"{$creditTx->description}\" a été remboursé au client, mais votre solde n'a pas pu être ajusté automatiquement (solde insuffisant). L'équipe support va vous contacter.",
                'is_read'      => false,
            ]);
        }

        // ── NOTIFY ADMINS ──
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'    => $admin->id,
                'type'       => $walletReversalFailed ? 'refund_wallet_reversal_failed' : 'refund_completed',
                'title'      => $walletReversalFailed ? 'Remboursement effectué — solde artisan non ajusté' : 'Remboursement effectué',
                'message'    => $walletReversalFailed
                    ? "Transaction #{$transaction->reference_id} remboursée au client, mais le solde de l'artisan n'a pas pu être débité automatiquement (solde insuffisant). Intervention manuelle nécessaire."
                    : "Transaction #{$transaction->reference_id} ({$transaction->amount} {$transaction->currency}) remboursée avec succès.",
                'is_read'    => false,
                'action_url' => '/admin/support-hq/tickets',
            ]);
        }
    }

    /**
     * Fulfill a Service Request payment from Client → Artisan.
     */
    protected function fulfillServiceRequestPayment(Transaction $transaction, ?string $kpayRef): void
    {
        $serviceRequest = ServiceRequest::find($transaction->item_id);

        if (!$serviceRequest) {
            Log::warning('K-PAY Webhook: fulfillServiceRequestPayment – serviceRequest not found', [
                'item_id' => $transaction->item_id,
            ]);
            return;
        }

        if ($serviceRequest->payment_status === 'paid') {
            Log::info("K-PAY Webhook: serviceRequest #{$serviceRequest->id} already marked as paid.");
            return;
        }

        // 1. Mark Service Request as PAID and COMPLETED
        $serviceRequest->update([
            'payment_status' => 'paid',
            'paid_at'        => now(),
            'status'         => 'completed',
            'completed_at'   => $serviceRequest->completed_at ?? now(),
        ]);

        // 2. Sync mission status if exists
        $mission = $serviceRequest->mission;
        if ($mission) {
            $mission->update([
                'status'            => 'completed',
                'commission_status' => 'paid',
                'payout_status'     => 'pending_payout',
                'end_date'          => now(),
            ]);
        }

        // 3. CREDIT ARTISAN WALLET
        $artisanId = $serviceRequest->artisan_id ?? $mission?->artisan_id ?? $serviceRequest->service?->artisan_id;
        if ($artisanId) {
            $artisan = User::find($artisanId);
            if ($artisan) {
                $wallet = $artisan->getOrCreateWallet();
                $wallet->credit(
                    amount: (float) $transaction->amount,
                    commissionAmount: (float) ($transaction->amount * 0.10), // 10% platform commission
                    fromUserId: $serviceRequest->user_id,
                    missionId: $mission?->id,
                    description: "Paiement reçu pour la prestation \"{$serviceRequest->requested_service_name}\"",
                    referenceId: $kpayRef ?? $transaction->reference_id
                );
            }
        }

        // 4. Send Notifications (Client & Artisan)
        Notification::create([
            'user_id'      => $serviceRequest->user_id,
            'type'         => 'service_request_paid',
            'related_type' => 'service_request',
            'related_id'   => $serviceRequest->id,
            'title'        => 'Paiement confirmé !',
            'message'      => "Votre paiement pour la prestation \"{$serviceRequest->requested_service_name}\" a été reçu avec succès. La prestation est désormais clôturée.",
            'is_read'      => false,
        ]);

        if ($artisanId) {
            Notification::create([
                'user_id'      => $artisanId,
                'type'         => 'payment_received',
                'related_type' => 'service_request',
                'related_id'   => $serviceRequest->id,
                'title'        => 'Paiement reçu sur votre portefeuille !',
                'message'      => "Le client a réglé la prestation \"{$serviceRequest->requested_service_name}\". Le solde a été crédité sur votre portefeuille.",
                'is_read'      => false,
            ]);
        }

        Log::info("K-PAY Webhook: ServiceRequest #{$serviceRequest->id} paid & completed. Artisan #{$artisanId} wallet credited.");
    }
}
