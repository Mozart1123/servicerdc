<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
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
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'user.type' => \App\Http\Middleware\SetUserType::class,
            'client' => \App\Http\Middleware\TypeMiddleware::class . ':client',
            'artisan' => \App\Http\Middleware\TypeMiddleware::class . ':artisan',
            'emploie' => \App\Http\Middleware\TypeMiddleware::class . ':job_seeker',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
