<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test predict provider with a Uganda MTN number format
// Uganda MTN numbers start with 256 + 77, 78, or 76
$testNumbers = [
    '256772000000', // MTN Uganda format
    '256712000000', // Airtel Uganda format
];

foreach ($testNumbers as $num) {
    $response = Illuminate\Support\Facades\Http::withHeaders([
        'X-API-Key' => env('KPAY_API_KEY'),
        'X-Secret-Key' => env('KPAY_SECRET_KEY'),
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post('https://admin.kpay.site/api/v1/payments/predict-provider', [
        'phoneNumber' => $num
    ]);
    
    echo $num . ' => Status: ' . $response->status() . "\n";
    echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
}
