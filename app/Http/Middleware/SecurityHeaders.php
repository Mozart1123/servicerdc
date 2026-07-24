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

        // Content-Security-Policy in Report-Only mode.
        // Permissive for now (unsafe-inline needed for Alpine.js x-data and onclick handlers
        // in Blade views) — use Report-Only to log violations without breaking the app.
        // Progressively tighten directives once violations are audited.
        $cspValue = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com http://localhost:* http://127.0.0.1:* http://[::1]:* http://*.kaspersky-labs.com ws://*.kaspersky-labs.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com http://localhost:* http://127.0.0.1:* http://[::1]:*",
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com data:",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' http://localhost:* ws://localhost:* http://127.0.0.1:* ws://127.0.0.1:* http://[::1]:* ws://[::1]:* https://*.pusher.com wss://*.pusher.com https://nominatim.openstreetmap.org http://*.kaspersky-labs.com ws://*.kaspersky-labs.com ws: wss:",
            "media-src 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);

        $response->headers->set('Content-Security-Policy-Report-Only', $cspValue);

        return $response;
    }
}
