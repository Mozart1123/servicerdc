<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'admin@proconnect.com')->first();
Illuminate\Support\Facades\Auth::login($user);

$controller = new App\Http\Controllers\Admin\UserController();
$request = Illuminate\Http\Request::create('/admin/users/flagged', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);

$response = $controller->flagged($request);
echo $response->getContent();
