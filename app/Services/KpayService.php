<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class KpayService
{
    protected $baseUrl;
    protected $apiKey;
    protected $secretKey;
    protected $webhookSecret;

    public function __construct()
    {
        $this->baseUrl = 'https://admin.kpay.site/api/v1';
        $this->apiKey = env('KPAY_API_KEY');
        $this->secretKey = env('KPAY_SECRET_KEY');
        // If no explicit webhook secret is provided, try falling back to secret key
        $this->webhookSecret = env('KPAY_WEBHOOK_SECRET', $this->secretKey);
    }

    /**
     * Get configured HTTP client for K-PAY API
     */
    protected function client()
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'X-Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
        ->withoutVerifying() // Bypass SSL cert issue on local Windows dev
        ->baseUrl($this->baseUrl);
    }

    /**
     * Initiate a payment (deposit) via USSD push or gateway
     *
     * Expected data:
     * - amount
     * - provider
     * - phoneNumber
     * - externalId
     * - description (optional)
     */
    public function initiatePayment(array $data)
    {
        $response = $this->client()->post('/payments/init', $data);

        if ($response->failed()) {
            Log::error('K-PAY Payment Init Failed', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new Exception('Failed to initiate payment: ' . $response->json('message', 'Unknown error'));
        }

        return $response->json();
    }

    /**
     * Initiate a payout (withdrawal)
     *
     * Expected data:
     * - amount
     * - provider
     * - phoneNumber
     * - externalId
     */
    public function initiatePayout(array $data)
    {
        $response = $this->client()->post('/payments/withdraw', $data);

        if ($response->failed()) {
            Log::error('K-PAY Payout Init Failed', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new Exception('Failed to initiate payout: ' . $response->json('message', 'Unknown error'));
        }

        return $response->json();
    }

    /**
     * Verify a transaction status
     */
    public function getTransactionStatus($id)
    {
        $response = $this->client()->get("/payments/{$id}");

        if ($response->failed()) {
            Log::error('K-PAY Transaction Status Check Failed', [
                'id' => $id,
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new Exception('Failed to fetch transaction status: ' . $response->json('message', 'Unknown error'));
        }

        return $response->json();
    }

    /**
     * Get wallet balance
     */
    public function getBalance()
    {
        $response = $this->client()->get("/payments/balance");

        if ($response->failed()) {
            Log::error('K-PAY Balance Check Failed', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new Exception('Failed to fetch wallet balance: ' . $response->json('message', 'Unknown error'));
        }

        return $response->json();
    }

    /**
     * Validate webhook signature
     */
    public function validateWebhookSignature($payload, $signature)
    {
        if (empty($signature) || empty($this->webhookSecret)) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Predict the provider for a given phone number
     */
    public function predictProvider(string $phoneNumber)
    {
        $response = $this->client()->post('/payments/predict-provider', [
            'phoneNumber' => preg_replace('/[^0-9]/', '', $phoneNumber)
        ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json('provider');
    }

    /**
     * Get the default currency for a provider
     */
    public function getCurrencyForProvider(string $provider): string
    {
        $map = [
            'MTN_MOMO_CMR' => 'XAF',
            'ORANGE_CMR'   => 'XAF',
            'VODACOM_MPESA_COD' => 'CDF',
            'AIRTEL_COD'   => 'CDF',
            'ORANGE_COD'   => 'CDF',
            'MTN_MOMO_CIV' => 'XOF',
            'ORANGE_CIV'   => 'XOF',
            'MTN_MOMO_BEN' => 'XOF',
            'MOOV_BEN'     => 'XOF',
            'AIRTEL_GAB'   => 'XAF',
            'MPESA_KEN'    => 'KES',
            'AIRTEL_COG'   => 'XAF',
            'MTN_MOMO_COG' => 'XAF',
            'AIRTEL_RWA'   => 'RWF',
            'MTN_MOMO_RWA' => 'RWF',
            'FREE_SEN'     => 'XOF',
            'ORANGE_SEN'   => 'XOF',
            'ORANGE_SLE'   => 'SLE',
            'AIRTEL_OAPI_UGA' => 'UGX',
            'MTN_MOMO_UGA' => 'UGX',
            'AIRTEL_OAPI_ZMB' => 'ZMW',
            'MTN_MOMO_ZMB' => 'ZMW',
            'ZAMTEL_ZMB'   => 'ZMW',
        ];

        return $map[$provider] ?? 'XAF'; // default to XAF
    }

    /**
     * Convert USD to the target currency using K-Pay exchange rates
     */
    public function convertUsdToLocal(float|string $usdAmount, string $targetCurrency): float
    {
        $usdAmount = (float) $usdAmount;
        if ($targetCurrency === 'USD') return $usdAmount;

        $response = $this->client()->get('/payments/exchange-rate', [
            'from' => 'USD',
            'to' => $targetCurrency
        ]);

        if ($response->successful()) {
            $rate = (float) $response->json('rate', 1.0);
            return $usdAmount * $rate;
        }

        // Fallbacks
        $fallbacks = [
            'XAF' => 600,
            'XOF' => 600,
            'CDF' => 2800,
            'KES' => 130,
        ];

        return $usdAmount * ($fallbacks[$targetCurrency] ?? 1.0);
    }
}
