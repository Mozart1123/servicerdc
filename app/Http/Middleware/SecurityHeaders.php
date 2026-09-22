<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     * Adds security-related HTTP response headers to every response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Prevent clickjacking — deny iframe embedding from other domains
        $response->headers->set('X-Frame-Options', 'DENY');

        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Control referrer information sent with requests
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // HSTS — only in production over HTTPS to avoid breaking local HTTP dev
        if (app()->environment('production') && $request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        // Dev-only sources (Vite hot reload over http/ws on localhost/127.0.0.1/[::1]).
        // Never included in production, and never any third-party antivirus/browser
        // extension domains — those don't belong in an app's CSP at all.
        $devSources = app()->environment('production')
            ? ''
            : ' http://localhost:* ws://localhost:* http://127.0.0.1:* ws://127.0.0.1:* http://[::1]:* ws://[::1]:*';

        // Content-Security-Policy — now enforced (was Report-Only).
        // Still permissive (unsafe-inline needed for Alpine.js x-data and onclick handlers
        // in Blade views). Progressively tighten directives (drop unsafe-inline/unsafe-eval)
        // once inline handlers are migrated to attached listeners / nonces.
        $cspValue = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com{$devSources}",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com{$devSources}",
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com data:",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' https://*.pusher.com wss://*.pusher.com https://nominatim.openstreetmap.org ws: wss:{$devSources}",
            "media-src 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);

        $response->headers->set('Content-Security-Policy', $cspValue);

        return $response;
    }
}