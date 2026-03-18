<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            // Si aucune session active n'est définie, on utilise le type par défaut de l'utilisateur
            if (!Session::has('active_user_type')) {
                Session::put('active_user_type', auth()->user()->user_type ?? 'client');
            }
        }

        return $next($request);
    }
}
