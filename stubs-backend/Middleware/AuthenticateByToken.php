<?php

declare(strict_types=1);

namespace App\Modules\Auth\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

// FILE: app/Modules/Auth/Middleware/AuthenticateByToken.php

class AuthenticateByToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get token from cookie
        $token = $request->cookie('auth_token');

        if ($token) {
            // Find token using Sanctum
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken) {
                // Login user for this request
                Auth::login($accessToken->tokenable);
            }
        }

        // If not authenticated, redirect to login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
