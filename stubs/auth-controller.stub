<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Resources\UserResource;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(private readonly AuthService $service)
    {
    }

    /**
     * Login user and return token.
     *
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Attempt to authenticate
        if ($this->service->login(
            $validated['email'],
            $validated['password'],
            $validated['remember'] ?? false
        )) {
            $user = $this->service->user();

            if (!$user) {
                return response()->json([
                    'status' => 0,
                    'data' => null,
                    'message' => 'Authentication failed',
                ], 401);
            }

            // Create Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            $cookie = cookie(
                'auth_token',
                $token,
                60 * 24,
                config('session.path', '/'),
                config('session.domain'),
                (bool) config('session.secure', $request->isSecure()),
                (bool) config('session.http_only', true),
                false,
                (string) config('session.same_site', 'lax')
            );

            return response()->json([
                'status' => 1,
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                ],
                'message' => 'Login successful',
            ])->withCookie($cookie);
        }

        return response()->json([
            'status' => 0,
            'data' => null,
            'message' => 'Invalid credentials',
        ], 401);
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Create user
        $user = $this->service->register($validated);

        // Auto login
        $this->service->login($validated['email'], $validated['password']);

        $token = $user->createToken('auth_token')->plainTextToken;

        $cookie = cookie(
            'auth_token',
            $token,
            60 * 24,
            config('session.path', '/'),
            config('session.domain'),
            (bool) config('session.secure', $request->isSecure()),
            (bool) config('session.http_only', true),
            false,
            (string) config('session.same_site', 'lax')
        );

        return response()->json([
            'status' => 1,
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
            'message' => 'Registration successful',
        ], 201)->withCookie($cookie);
    }

    /**
     * Logout the current user.
     */
    public function logout(Request $request): JsonResponse
    {
        // Delete current access token
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'status' => 1,
            'data' => null,
            'message' => 'Logout successful',
        ])->withCookie(cookie()->forget('auth_token'));
    }

    /**
     * Get the authenticated user.
     */
    public function me(): JsonResponse
    {
        $user = $this->service->user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'data' => null,
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'status' => 1,
            'data' => new UserResource($user),
            'message' => 'User fetched successfully',
        ]);
    }
}
