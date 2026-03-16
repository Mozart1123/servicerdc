<?php

use App\Services\SystemActivityService;
use App\Models\SystemLog;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Triggering Alert Tests...\n";

// 1. Trigger a critical security log (should create immediate alert)
echo "Logging critical security event...\n";
SystemActivityService::log('SEC', 'Tentative d\'intrusion détectée sur le port 22 (SSH)', 'critical', ['port' => 22]);

// 2. Trigger Brute Force (5 failures)
echo "Simulating brute force from IP 1.2.3.4...\n";
for ($i = 0; $i < 6; $i++) {
    SystemActivityService::log('AUTH', 'Échec de connexion pour utilisateur admin', 'error', ['ip' => '1.2.3.4']);
}

echo "Done! Check the Admin Alerts page.\n";
