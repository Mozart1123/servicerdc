<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserTypeIsSelected
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && empty($user->user_type)) {
            // Avoid redirect loops on selection routes or logout
            if (
                $request->routeIs('auth.select-user-type*') ||
                $request->routeIs('logout') ||
                $request->is('auth/select-user-type*') ||
                $request->is('logout')
            ) {
                return $next($request);
            }

            return redirect()->route('auth.select-user-type');
        }

        return $next($request);
    }
}
