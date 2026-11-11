<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Append baseline HTTP security headers to all application responses.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->remove('X-Powered-By');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        $isDev = app()->environment(['local', 'testing']);
        $viteHttp = $isDev ? ' http://localhost:5173 http://127.0.0.1:5173' : '';
        $viteWs = $isDev ? ' ws://localhost:5173 ws://127.0.0.1:5173' : '';

        $cspDirectives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'{$viteHttp}",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net{$viteHttp}",
            "font-src 'self' https://fonts.bunny.net data:",
            "img-src 'self' data: https: http:",
            "connect-src 'self'{$viteHttp}{$viteWs}",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
