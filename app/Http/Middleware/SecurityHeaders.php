<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        $headers = config('security.headers', []);

        $response->headers->set('X-Frame-Options', (string) ($headers['x_frame_options'] ?? 'SAMEORIGIN'));
        $response->headers->set('X-Content-Type-Options', (string) ($headers['x_content_type_options'] ?? 'nosniff'));
        $response->headers->set('Referrer-Policy', (string) ($headers['referrer_policy'] ?? 'strict-origin-when-cross-origin'));
        $response->headers->set('Permissions-Policy', (string) ($headers['permissions_policy'] ?? 'camera=(), microphone=(), geolocation=()'));
        $response->headers->set('Content-Security-Policy', (string) ($headers['content_security_policy'] ?? "default-src 'self'"));
        $response->headers->set('Cross-Origin-Opener-Policy', (string) ($headers['cross_origin_opener_policy'] ?? 'same-origin'));
        $response->headers->set('Cross-Origin-Resource-Policy', (string) ($headers['cross_origin_resource_policy'] ?? 'same-origin'));

        if ($this->shouldSendHsts($request)) {
            $response->headers->set('Strict-Transport-Security', $this->buildHstsValue());
        }

        return $response;
    }

    private function shouldSendHsts(Request $request): bool
    {
        if (! config('security.hsts.enabled', true)) {
            return false;
        }

        return $request->isSecure() || (bool) config('security.force_https', false);
    }

    private function buildHstsValue(): string
    {
        $parts = ['max-age='.(int) config('security.hsts.max_age', 31536000)];

        if ((bool) config('security.hsts.include_subdomains', true)) {
            $parts[] = 'includeSubDomains';
        }

        if ((bool) config('security.hsts.preload', false)) {
            $parts[] = 'preload';
        }

        return implode('; ', $parts);
    }
}
