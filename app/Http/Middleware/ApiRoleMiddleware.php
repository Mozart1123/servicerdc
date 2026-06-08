<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * API Role Middleware
 *
 * Checks the user's `user_type` field for API routes.
 * Allowed types: artisan, client, recruiter, job_seeker
 *
 * Usage: ->middleware('api.role:artisan')
 *        ->middleware('api.role:client,recruiter')
 */
class ApiRoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$types): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Super admins and admins bypass type restrictions
        if (in_array($user->role, ['admin', 'super_admin'], true)) {
            return $next($request);
        }

        $userType = $user->user_type ?? $user->role;

        if (!in_array($userType, $types, true)) {
            return response()->json([
                'message' => 'Accès refusé. Votre rôle ne vous permet pas d\'accéder à cette ressource.',
                'required_types' => $types,
                'your_type'      => $userType,
            ], 403);
        }

        return $next($request);
    }
}
