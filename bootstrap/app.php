<?php

use App\Http\Middleware\SecurityHeaders;
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
        $middleware->statefulApi();

        $middleware->web(append: [
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        $middleware->preventRequestsDuringMaintenance(except: [
            '/admin/*',
            '/login',
        ]);

        // Append security headers to every HTTP response (web + API)
        $middleware->append(SecurityHeaders::class);

        // Register custom middleware aliases
        $middleware->alias([
            'role'                     => \App\Http\Middleware\RoleMiddleware::class,
            'api.role'                 => \App\Http\Middleware\ApiRoleMiddleware::class,
            'prevent.client.dashboard' => \App\Http\Middleware\PreventClientDashboardAccess::class,
            'ensure.user_type'         => \App\Http\Middleware\EnsureUserTypeIsSelected::class,
            'ensure.recruitment'       => \App\Http\Middleware\EnsureRecruitmentEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect()->route('login')->with('error', 'Votre session a expiré, veuillez vous reconnecter.');
        });
    })->create();
