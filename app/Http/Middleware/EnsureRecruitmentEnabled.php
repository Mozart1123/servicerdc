<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks access to any recruitment-related route (public job listings,
 * applications, CV management, recruiter public profiles, offer
 * management) while config('features.recruitment_enabled') is false —
 * sends the visitor back home with a message instead of a broken page.
 *
 * Purely a display/routing gate: no data is touched. Flip the config
 * value back to true to restore access instantly.
 */
class EnsureRecruitmentEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('features.recruitment_enabled')) {
            return redirect()->route('home')
                ->with('info', "La fonctionnalité de recrutement n'est pas disponible pour le moment.");
        }

        return $next($request);
    }
}
