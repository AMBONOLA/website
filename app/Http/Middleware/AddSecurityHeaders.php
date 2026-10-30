<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    /**
     * The Content-Security-Policy is the last line of defense against XSS: even if HTML got past
     * the sanitizer, the browser refuses to run any script that isn't ours (same origin or nonce'd).
     * Inline styles stay allowed because insight bodies use style attributes (filtered by the sanitizer).
     */
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        $response = $next($request);

        // Laravel's debug error page relies on inline scripts. Debug is off in production, so the policy always applies there.
        if (! (config('app.debug') && $response->isServerError())) {
            $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy());
        }

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        return $response;
    }

    private function contentSecurityPolicy(): string
    {
        $nonce = Vite::cspNonce();
        // CSP can't name IPv6 hosts like [::1], so the local Vite dev server is allowed by scheme. Never in production.
        $dev_server = app()->isLocal() && Vite::isRunningHot() ? ' http: ws:' : '';

        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}'{$dev_server}",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net{$dev_server}",
            'font-src \'self\' https://fonts.bunny.net',
            "img-src 'self' data:",
            "connect-src 'self'{$dev_server}",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]);
    }
}
