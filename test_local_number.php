<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$num = '0784630448';
$response = Illuminate\Support\Facades\Http::withHeaders([
    'X-API-Key' => env('KPAY_API_KEY'),
    'X-Secret-Key' => env('KPAY_SECRET_KEY'),
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
])
->withoutVerifying()
->post('https://admin.kpay.site/api/v1/payments/predict-provider', [
    'phoneNumber' => $num
]);

echo $num . ' => Status: ' . $response->status() . "\n";
echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";

