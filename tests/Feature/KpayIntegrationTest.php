<?php

namespace Tests\Feature;

use App\Services\KpayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KpayIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.kpay.api_key', 'kpay_test_key');
        config()->set('services.kpay.secret_key', 'test-secret');
        config()->set('services.kpay.webhook_secret', 'webhook-secret');
        config()->set('services.kpay.base_url', 'https://admin.kpay.site/api/v1');
    }

    public function test_balance_returns_wallets_when_kpay_succeeds(): void
    {
        Http::fake([
            'https://admin.kpay.site/api/v1/payments/balance' => Http::response([
                ['currency' => 'CDF', 'balance' => 1000, 'availableBalance' => 900],
            ]),
        ]);

        $wallets = app(KpayService::class)->getBalance();

        $this->assertSame('CDF', $wallets[0]['currency']);
        Http::assertSent(fn ($request) => $request->hasHeader('X-API-Key', 'kpay_test_key')
            && $request->hasHeader('X-Secret-Key', 'test-secret'));
    }

    public function test_balance_throws_when_production_api_is_blocked(): void
    {
        Http::fake([
            'https://admin.kpay.site/api/v1/payments/balance' => Http::response([
                'message' => 'Production API blocked',
            ], 403),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to fetch wallet balance: Production API blocked');

        app(KpayService::class)->getBalance();
    }

    public function test_webhook_accepts_a_valid_signature(): void
    {
        $payload = json_encode([
            'event' => 'payment.completed',
            'paymentId' => 'tx-valid',
            'externalId' => 'unknown-reference',
            'status' => 'COMPLETED',
        ], JSON_THROW_ON_ERROR);

        $response = $this->call('POST', '/api/webhooks/kpay', [], [], [], [
            'HTTP_X_KPAY_SIGNATURE' => hash_hmac('sha256', $payload, 'webhook-secret'),
            'CONTENT_TYPE' => 'application/json',
        ], $payload);

        $response->assertOk();
    }

    public function test_webhook_rejects_an_invalid_signature(): void
    {
        $payload = '{"event":"payment.completed","paymentId":"tx-invalid"}';

        $this->call('POST', '/api/webhooks/kpay', [], [], [], [
            'HTTP_X_KPAY_SIGNATURE' => 'invalid',
            'CONTENT_TYPE' => 'application/json',
        ], $payload)
            ->assertUnauthorized();
    }

    public function test_duplicate_webhook_event_is_ignored(): void
    {
        $payload = json_encode([
            'event' => 'payment.completed',
            'paymentId' => 'tx-duplicate',
            'externalId' => 'unknown-reference',
            'status' => 'COMPLETED',
        ], JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $payload, 'webhook-secret');

        $this->call('POST', '/api/webhooks/kpay', [], [], [], [
            'HTTP_X_KPAY_SIGNATURE' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $payload)
            ->assertOk();

        $this->call('POST', '/api/webhooks/kpay', [], [], [], [
            'HTTP_X_KPAY_SIGNATURE' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $payload)
            ->assertOk()
            ->assertJson(['message' => 'Webhook already processed']);
    }
}
