<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// FILE: app/Http/Middleware/SecurityHeaders.php

class SecurityHeaders
{
    /**
     * Add baseline security headers to all application responses.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Restrict browser features
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');
        
        // Content Security Policy (report only mode - switch to enforce when ready)
        $response->headers->set(
            'Content-Security-Policy-Report-Only',
            $this->build_csp_policy()
        );

        // HSTS for production/secure connections
        if (app()->environment('production') || $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Remove X-Powered-By header
        $response->headers->remove('X-Powered-By');

        return $response;
    }

    /**
     * Build Content Security Policy rules.
     */
    private function build_csp_policy(): string
    {
        $script_src = ["'self'"];
        $style_src = ["'self'", "'unsafe-inline'", 'https://fonts.bunny.net'];
        $font_src = ["'self'", 'data:', 'https://fonts.bunny.net'];
        $connect_src = ["'self'"];

        // Allow Vite dev server in local environment
        if (app()->environment('local')) {
            $script_src[] = 'http://localhost:5173';
            $style_src[] = 'http://localhost:5173';
            $connect_src[] = 'http://localhost:5173';
            $connect_src[] = 'ws://localhost:5173';
        }

        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "frame-ancestors 'self'",
            "object-src 'none'",
            "form-action 'self'",
            'img-src ' . implode(' ', ["'self'", 'data:', 'blob:']),
            'script-src ' . implode(' ', array_unique($script_src)),
            'style-src ' . implode(' ', array_unique($style_src)),
            'font-src ' . implode(' ', array_unique($font_src)),
            'connect-src ' . implode(' ', array_unique($connect_src)),
        ];

        return implode('; ', $directives) . ';';
    }
}
