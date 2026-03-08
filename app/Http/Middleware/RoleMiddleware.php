<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dynamic Role-Based Access Control Middleware
 * 
 * This middleware handles role-based access control with support for multiple roles.
 * Usage in routes: ->middleware('role:admin,super_admin')
 * 
 * @package App\Http\Middleware
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): Response $next
     * @param string ...$roles Variadic parameter for multiple allowed roles
     * @return Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        $user = Auth::user();

        // Strict type check: ensure user has a role property
        if (!isset($user->role) || !is_string($user->role)) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte n\'a pas de rôle assigné. Contactez l\'administrateur.');
        }

        // Check if user's role is in the allowed roles array
        if (!in_array($user->role, $roles, true)) {
            // Log unauthorized access attempt
            \Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'required_roles' => $roles,
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);

            abort(403, 'Accès non autorisé. Vous n\'avez pas les permissions nécessaires.');
        }

        return $next($request);
    }
}
