<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the last 3 pending transactions to see what's happening
$transactions = App\Models\Transaction::orderBy('id', 'desc')->take(5)->get();
foreach ($transactions as $t) {
    echo "ID: {$t->id} | Status: {$t->status} | KPay Ref: {$t->kpay_reference} | External: {$t->reference_id} | Amount: {$t->amount} {$t->currency}" . PHP_EOL;
    
    // If there's a kpay_reference, poll the status from KPay
    if ($t->kpay_reference) {
        $k = new App\Services\KpayService();
        try {
            $res = $k->getTransactionStatus($t->kpay_reference);
            echo "  -> KPay Status: " . ($res['status'] ?? 'unknown') . PHP_EOL;
            echo "  -> KPay Data: " . json_encode($res) . PHP_EOL;
        } catch (\Exception $e) {
            echo "  -> Error: " . $e->getMessage() . PHP_EOL;
        }
    }
    echo PHP_EOL;
}
