<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$response = Illuminate\Support\Facades\Http::withHeaders([
    'X-API-Key' => env('KPAY_API_KEY'),
    'X-Secret-Key' => env('KPAY_SECRET_KEY'),
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
])->get('https://admin.kpay.site/api/v1/payments/exchange-rate?from=USD&to=XAF');

var_dump($response->status());
var_dump($response->json());
