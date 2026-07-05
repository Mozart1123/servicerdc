<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\KpayService;
use App\Models\Transaction;
use App\Models\Mission;
use App\Models\Service;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $kpayService;

    public function __construct(KpayService $kpayService)
    {
        $this->kpayService = $kpayService;
    }

    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string',
            'phone_number' => 'required|string',
            'payment_type' => 'required|string|in:mission,service,subscription',
            'reference_id' => 'required|integer', // The ID of the item being paid for
            'description' => 'nullable|string',
        ]);

        $user = $request->user();
        
        // 1. Amount Calculation
        $amount = 0;
        switch ($validated['payment_type']) {
            case 'mission':
                $mission = Mission::findOrFail($validated['reference_id']);
                $amount = $mission->amount;
                break;
            case 'service':
                // As requested, if services can be negotiated, use Mission. 
                // But if they fall back to Service, use Service::price.
                $service = Service::findOrFail($validated['reference_id']);
                $amount = $service->price;
                break;
            case 'subscription':
                $plan = SubscriptionPlan::findOrFail($validated['reference_id']);
                $amount = $plan->price_monthly;
                break;
        }

        // 3. Currency Conversion (USD to Local)
        $providerCurrency = $this->kpayService->getCurrencyForProvider($validated['provider']);
        $localAmount = (int) round($this->kpayService->convertUsdToLocal($amount, $providerCurrency));

        if ($localAmount < 50) {
            return response()->json(['error' => 'Amount must be at least 50 ' . $providerCurrency], 400);
        }

        // 4. Generate a unique externalId for K-PAY (idempotency key)
        $externalId = 'PAY-' . time() . '-' . Str::random(5);

        // 5. Create the Transaction locally in a PENDING state
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $localAmount; // Store local amount
        $transaction->currency = $providerCurrency; 
        $transaction->status = 'pending';
        $transaction->type = $validated['payment_type'];
        $transaction->reference_id = $externalId; 
        $transaction->item_id = $validated['reference_id'];
        $transaction->description = "Payment for {$validated['payment_type']} #{$validated['reference_id']}" . ($validated['description'] ? " - {$validated['description']}" : '');
        
        if ($validated['payment_type'] === 'mission') {
            $transaction->mission_id = $validated['reference_id'];
        }

        $transaction->save();

        // 6. Initiate payment via K-PAY API
        try {
            $kpayResponse = $this->kpayService->initiatePayment([
                'amount' => $localAmount,
                'provider' => $validated['provider'],
                'phoneNumber' => $validated['phone_number'],
                'externalId' => $externalId, 
                'description' => $transaction->description,
                'customerName' => $user->name,
                'customerEmail' => $user->email,
            ]);

            if (isset($kpayResponse['reference'])) {
                $transaction->kpay_reference = $kpayResponse['reference'];
                $transaction->save();
            }

            return response()->json([
                'message' => 'Payment initiated successfully. Please check your phone for the USSD prompt.',
                'transaction' => $transaction,
                'kpay_data' => $kpayResponse
            ], 201);

        } catch (\Exception $e) {
            $transaction->status = 'failed';
            $transaction->save();

            return response()->json([
                'error' => 'Failed to initiate payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
