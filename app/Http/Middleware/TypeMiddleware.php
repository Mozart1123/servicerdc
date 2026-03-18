<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $type
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check user type from session or model
        $activeType = session('active_user_type', $user->user_type);

        if ($activeType !== $type) {
            // Special case: 'emploie' (job_seeker) can see 'client' content as requested
            if ($type === 'client' && $activeType === 'job_seeker') {
                return $next($request);
            }

            abort(403, 'Accès non autorisé pour votre type de compte.');
        }

        return $next($request);
    }
}
