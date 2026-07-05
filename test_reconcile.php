<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\Transaction::where('status', 'pending')->whereNotNull('kpay_reference')->get()->each(function($t) {
    $k = new App\Services\KpayService();
    try {
        $res = $k->getTransactionStatus($t->kpay_reference);
        if(isset($res['status']) && $res['status'] === 'COMPLETED') {
            $t->status = 'succeeded';
            $t->save();
            $s = App\Models\Subscription::firstOrNew(['user_id' => $t->user_id, 'subscription_plan_id' => $t->item_id]);
            $s->status = 'active';
            $s->paid_at = now();
            $s->starts_at = now();
            $s->ends_at = now()->addMonth();
            $s->transaction_ref = $t->kpay_reference;
            $s->amount_paid = $t->amount;
            $s->save();
            echo 'OK: ' . $t->id . PHP_EOL;
        } else {
            echo 'PENDING/FAILED: ' . $t->id . ' - ' . ($res['status'] ?? 'unknown') . PHP_EOL;
        }
    } catch (\Exception $e) {
        echo 'ERROR: ' . $t->id . ' - ' . $e->getMessage() . PHP_EOL;
    }
});
