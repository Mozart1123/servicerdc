<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cache the maintenance_mode value for 60 seconds
        $maintenanceMode = Cache::remember('maintenance_mode', 60, function () {
            return Setting::where('key', 'maintenance_mode')->value('value') ?? '0';
        });

        if ($maintenanceMode === '1') {
            // Bypass checking for /login, /logout, /super-admin, and /super-admin/*
            // This allows the super admin to log in, log out, and access admin features.
            if ($request->is('login') || $request->is('logout') || $request->is('super-admin') || $request->is('super-admin/*')) {
                return $next($request);
            }

            // Also check if the authenticated user is a super admin
            $user = auth()->user();
            if ($user && $user->role === 'super_admin') {
                return $next($request);
            }

            // Return custom maintenance page with a 503 status code
            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
