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
        // IMPORTANT: read credentials via config(), never env() directly here.
        // Once `php artisan config:cache` has run (part of our deploy routine via
        // `php artisan optimize`), Laravel stops reading the .env file at runtime —
        // any env('KPAY_...') call outside of a config/*.php file silently returns
        // null in that state, which is what caused the "API Key and Secret Key are
        // required" 401 errors in production. config('services.kpay.*') is safe
        // because config/services.php's own env() calls are baked into the cache
        // when it's built.
        $this->apiKey = config('services.kpay.api_key');
        $this->secretKey = config('services.kpay.secret_key');
        // If no explicit webhook secret is provided, try falling back to secret key
        $this->webhookSecret = config('services.kpay.webhook_secret') ?: $this->secretKey;
    }

    /**
     * Get configured HTTP client for K-PAY API
     */
    protected function client()
    {
        $client = Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'X-Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
        ->baseUrl($this->baseUrl);

        // SSL verification is only bypassed on local dev machines with a broken
        // certificate store. It must NEVER be disabled in production/staging —
        // this client carries live payment credentials and financial data, and
        // disabling verification allows a man-in-the-middle to intercept or
        // tamper with API keys, payment requests, and balance responses.
        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        return $client;
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
            throw new Exception($response->json('message', 'Unknown error'), $response->status());
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
     * Get the current USD -> target currency exchange rate.
     *
     * Tries K-Pay's live exchange-rate endpoint first (so the app always reflects
     * the real rate the operator will actually apply); falls back to a static
     * approximation only if that call fails, so payments never hard-fail just
     * because the rate endpoint is briefly unavailable.
     *
     * NOTE: per K-Pay's own API reference, GET /payments/exchange-rate is
     * documented for conversions BETWEEN THEIR SUPPORTED ZONE CURRENCIES
     * (XAF, XOF, KES, CDF, UGX, RWF, SLE, ZMW) — USD is not listed among
     * them. It's unconfirmed whether `from=USD` is actually honored server
     * side or silently ignored/defaulted. Until this is verified against
     * the live API (or confirmed with K-Pay support), assume this call may
     * always be falling through to the hardcoded $fallbacks below — meaning
     * the "live" rate could really be a static one that goes stale over
     * time. Worth a one-off manual check: call this endpoint directly with
     * real prod keys (GET /payments/exchange-rate?from=USD&to=CDF) and log
     * what comes back.
     */
    public function getExchangeRate(string $targetCurrency): float
    {
        if ($targetCurrency === 'USD') {
            return 1.0;
        }

        try {
            $response = $this->client()->get('/payments/exchange-rate', [
                'from' => 'USD',
                'to' => $targetCurrency,
            ]);

            if ($response->successful()) {
                return (float) $response->json('rate', 1.0);
            }
        } catch (\Throwable $e) {
            Log::warning('K-PAY exchange rate lookup failed, using fallback rate', [
                'target' => $targetCurrency,
                'error' => $e->getMessage(),
            ]);
        }

        // Fallbacks (approximate — used only when the live rate endpoint is unreachable)
        $fallbacks = [
            'XAF' => 600,
            'XOF' => 600,
            'CDF' => 2800,
            'KES' => 130,
        ];

        return (float) ($fallbacks[$targetCurrency] ?? 1.0);
    }

    /**
     * Convert USD to the target currency using K-Pay exchange rates
     */
    public function convertUsdToLocal(float|string $usdAmount, string $targetCurrency): float
    {
        $usdAmount = (float) $usdAmount;
        if ($targetCurrency === 'USD') return $usdAmount;

        return $usdAmount * $this->getExchangeRate($targetCurrency);
    }

    /**
     * Refund a completed payment via K-PAY.
     *
     * Per K-PAY's API reference:
     * - Full amount only — K-PAY does not support partial refunds.
     * - Must be requested within 7 days of the original payment.
     * - Only one active refund is allowed per payment.
     * K-PAY enforces all three rules server-side; we just surface whatever
     * error it returns (e.g. "refund window expired", "refund already exists").
     *
     * @param string $paymentId K-PAY's own payment id (our `kpay_reference` column — NOT our externalId).
     */
    public function refundPayment(string $paymentId, ?string $reason = null): array
    {
        $response = $this->client()->post("/payments/{$paymentId}/refund", array_filter([
            'reason' => $reason,
        ]));

        if ($response->failed()) {
            Log::error('K-PAY Refund Failed', [
                'paymentId' => $paymentId,
                'status'    => $response->status(),
                'body'      => $response->json(),
            ]);
            throw new Exception($response->json('message', 'Unknown error'), $response->status());
        }

        return $response->json();
    }
}
