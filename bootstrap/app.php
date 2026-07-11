<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->preventRequestsDuringMaintenance(except: [
            '/admin/*',
            '/login',
        ]);

        // Register custom middleware aliases
        $middleware->alias([
            'role'                     => \App\Http\Middleware\RoleMiddleware::class,
            'api.role'                 => \App\Http\Middleware\ApiRoleMiddleware::class,
            'prevent.client.dashboard' => \App\Http\Middleware\PreventClientDashboardAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
