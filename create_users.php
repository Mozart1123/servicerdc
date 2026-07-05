<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$configs = [
    ['email' => 'admin@proconnect.cd', 'role' => 'admin', 'user_type' => 'client'],
    ['email' => 'artisan@proconnect.cd', 'role' => 'user', 'user_type' => 'artisan'],
    ['email' => 'client@proconnect.cd', 'role' => 'user', 'user_type' => 'client'],
];

foreach($configs as $config) {
    $user = App\Models\User::firstOrCreate(
        ['email' => $config['email']],
        [
            'name' => ucfirst($config['user_type']) . ' Test', 
            'password' => bcrypt('password123'),
            'role' => $config['role'],
            'user_type' => $config['user_type'],
            'status' => 'active'
        ]
    );
    $user->role = $config['role'];
    $user->user_type = $config['user_type'];
    $user->save();
    echo 'Created ' . $config['email'] . ' account.' . PHP_EOL;
}
