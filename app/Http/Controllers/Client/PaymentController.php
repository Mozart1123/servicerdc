<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\KpayService;
use App\Models\Transaction;
use App\Models\KpayTransaction;
use App\Models\Mission;
use App\Models\Service;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $kpayService;

    public function __construct(KpayService $kpayService)
    {
        $this->kpayService = $kpayService;
    }

    /**
     * Initiate a USSD-push K-PAY payment.
     *
     * Cinématique :
     *   1. Valider la requête
     *   2. Calculer le montant (USD → devise locale)
     *   3. Créer Transaction (pending) + KpayTransaction (pending) en DB
     *   4. Appeler K-PAY → push USSD sur le téléphone du client
     *   5. Retourner 201 immédiatement — la confirmation arrive via webhook
     */
    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'provider'     => 'required|string',
            'phone_number' => 'required|string',
            'payment_type' => 'required|string|in:mission,service,subscription',
            'reference_id' => 'required|integer',
            'description'  => 'nullable|string',
        ]);

        $user = $request->user();

        // ── 1. AMOUNT CALCULATION ──────────────────────────────────────────────
        $amountUsd = 0;
        $missionId = null;

        switch ($validated['payment_type']) {
            case 'mission':
                $mission   = Mission::findOrFail($validated['reference_id']);
                $amountUsd = $mission->amount;
                $missionId = $mission->id;
                break;

            case 'service':
                $service   = Service::findOrFail($validated['reference_id']);
                $amountUsd = $service->price;
                break;

            case 'subscription':
                $plan      = SubscriptionPlan::findOrFail($validated['reference_id']);
                $amountUsd = $plan->price_monthly;
                break;
        }

        // ── 2. CURRENCY CONVERSION (USD → LOCAL) ──────────────────────────────
        $providerCurrency = $this->kpayService->getCurrencyForProvider($validated['provider']);
        $localAmount      = (int) round($this->kpayService->convertUsdToLocal($amountUsd, $providerCurrency));

        if ($localAmount < 50) {
            return response()->json([
                'error' => 'Le montant doit être d\'au moins 50 ' . $providerCurrency,
            ], 400);
        }

        // ── 3. GENERATE IDEMPOTENCY KEY ────────────────────────────────────────
        $externalId  = 'PAY-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8));
        $description = "Paiement {$validated['payment_type']} #{$validated['reference_id']}"
            . ($validated['description'] ? ' — ' . $validated['description'] : '');

        // ── 4. PERSIST RECORDS IN DB (atomic) ─────────────────────────────────
        try {
            DB::transaction(function () use (
                $user, $localAmount, $providerCurrency, $validated,
                $externalId, $description, $missionId, &$transaction, &$kpayTx
            ) {
                // Generic transaction record (shared audit table)
                $transaction = Transaction::create([
                    'user_id'      => $user->id,
                    'mission_id'   => $missionId,
                    'amount'       => $localAmount,
                    'currency'     => $providerCurrency,
                    'status'       => 'pending',
                    'type'         => $validated['payment_type'],
                    'reference_id' => $externalId,
                    'item_id'      => $validated['reference_id'],
                    'description'  => $description,
                ]);

                // Dedicated K-PAY audit record (idempotency anchor)
                if ($missionId) {
                    $kpayTx = KpayTransaction::create([
                        'mission_id'      => $missionId,
                        'transaction_ref' => $externalId,
                        'phone_number'    => $validated['phone_number'],
                        'amount'          => $localAmount,
                        'status'          => 'pending',
                        'raw_response'    => null,
                    ]);
                }
            });

        } catch (\Exception $e) {
            Log::error('PaymentController: failed to persist transaction', [
                'error' => $e->getMessage(),
                'user'  => $user->id,
            ]);
            return response()->json(['error' => 'Erreur lors de la création de la transaction.'], 500);
        }

        // ── 5. CALL K-PAY → USSD PUSH ─────────────────────────────────────────
        try {
            $kpayResponse = $this->kpayService->initiatePayment([
                'amount'        => $localAmount,
                'provider'      => $validated['provider'],
                'phoneNumber'   => $validated['phone_number'],
                'externalId'    => $externalId,
                'description'   => $description,
                'customerName'  => $user->name,
                'customerEmail' => $user->email,
            ]);

            // Store K-PAY's own reference if returned immediately
            if (isset($kpayResponse['reference'])) {
                $transaction->kpay_reference = $kpayResponse['reference'];
                $transaction->save();
            }

            return response()->json([
                'message'    => 'Paiement initié. Veuillez confirmer le prompt USSD sur votre téléphone.',
                'reference'  => $externalId,
                'amount'     => $localAmount,
                'currency'   => $providerCurrency,
                'kpay_data'  => $kpayResponse,
            ], 201);

        } catch (\Exception $e) {
            // Mark both records as failed
            $transaction->status = 'failed';
            $transaction->save();

            if (!empty($kpayTx)) {
                $kpayTx->status       = 'failed';
                $kpayTx->raw_response = ['error_message' => $e->getMessage(), 'status_code' => $e->getCode()];
                $kpayTx->save();
            }

            $kpayStatus = $e->getCode();
            
            // Map to generic messages for the frontend
            $clientMessage = match (true) {
                $kpayStatus >= 400 && $kpayStatus < 500 => 'Les informations fournies sont invalides, l\'opérateur a refusé la requête, ou les fonds sont insuffisants.',
                $kpayStatus >= 500 => 'Le service de paiement partenaire est temporairement indisponible. Veuillez réessayer plus tard.',
                default => 'Une erreur est survenue lors de l\'initiation du paiement.',
            };
            
            // HTTP response code for our API: 
            // 422 for upstream business logic errors (like insufficient funds)
            // 502 for upstream unavailability
            $httpStatus = ($kpayStatus >= 500) ? 502 : 422;

            Log::error('PaymentController: K-PAY initiation failed', [
                'raw_kpay_error' => $e->getMessage(),
                'kpay_status'    => $kpayStatus,
                'externalId'     => $externalId,
            ]);

            return response()->json([
                'error' => $clientMessage,
            ], $httpStatus);
        }
    }

    /**
     * Return the payment status for a given reference (used by front-end polling fallback).
     */
    public function checkStatus(Request $request, string $ref)
    {
        $kpayTx = KpayTransaction::where('transaction_ref', $ref)->first();

        if (!$kpayTx) {
            return response()->json(['error' => 'Transaction introuvable.'], 404);
        }

        return response()->json([
            'reference' => $kpayTx->transaction_ref,
            'status'    => $kpayTx->status,
            'amount'    => $kpayTx->amount,
            'updated_at' => $kpayTx->updated_at,
        ]);
    }
}
