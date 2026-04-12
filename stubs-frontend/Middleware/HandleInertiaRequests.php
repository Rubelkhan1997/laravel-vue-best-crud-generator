<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

// FILE: app/Http/Middleware/HandleInertiaRequests.php

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            'auth' => [
                'user' => $this->getUserFromToken($request),
            ],

            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ];
    }

    /**
     * Get user from token cookie or session
     */
    private function getUserFromToken(Request $request): ?array
    {
        // Try to get token from cookie
        $token = $request->cookie('auth_token');

        if ($token) {
            // Find user by token using Sanctum
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

            if ($accessToken && $accessToken->tokenable instanceof \App\Models\User) {
                return $this->mapUser($accessToken->tokenable);
            }
        }

        // Fallback to session-based auth
        if ($request->user()) {
            return $this->mapUser($request->user());
        }

        return null;
    }

    /**
     * Map user for Inertia shared props.
     */
    private function mapUser(\App\Models\User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->roles()->first()?->name,
            'roles' => $user->getRoleNames()->values(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'created_at' => $user->created_at?->toIso8601String(),
            'updated_at' => $user->updated_at?->toIso8601String(),
        ];
    }
}
