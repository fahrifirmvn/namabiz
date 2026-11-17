<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiResponse;
use App\Support\GoogleIdTokenVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user and generate a Sanctum Bearer token.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:8'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $deviceName = $validated['device_name'] ?? 'android-device';
        $token = $user->createToken($deviceName)->plainTextToken;

        return ApiResponse::success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Registration successful', 201);
    }

    /**
     * Authenticate an existing user and generate a Sanctum Bearer token.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return ApiResponse::error('Invalid email or password credentials.', 401);
        }

        $deviceName = $validated['device_name'] ?? 'android-device';
        $token = $user->createToken($deviceName)->plainTextToken;

        return ApiResponse::success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login successful');
    }

    /**
     * Authenticate or register via Google One-Tap on mobile after cryptographic ID token verification.
     */
    public function google(Request $request, GoogleIdTokenVerifier $verifier): JsonResponse
    {
        $validated = $request->validate([
            'id_token' => ['required', 'string', 'min:20'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $claims = $verifier->verify($validated['id_token']);
        } catch (\RuntimeException $exception) {
            $status = str_contains($exception->getMessage(), 'not configured') ? 503 : 401;

            return ApiResponse::error($exception->getMessage(), $status);
        }

        $existingUser = User::where('email', $claims['email'])->first();
        if ($existingUser && $existingUser->isAdmin()) {
            return ApiResponse::error('Administrator accounts cannot authenticate via social login.', 403);
        }

        $user = $existingUser ?? User::create([
            'email' => $claims['email'],
            'name' => $claims['name'] ?? explode('@', $claims['email'])[0],
            'password' => Hash::make(Str::random(32)),
            'avatar' => $claims['avatar'],
        ]);

        if (! $existingUser) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $deviceName = $validated['device_name'] ?? 'android-google-auth';
        $token = $user->createToken($deviceName)->plainTextToken;

        return ApiResponse::success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Google authentication successful');
    }

    /**
     * Get the authenticated user's profile and subscription tier.
     */
    public function user(Request $request): JsonResponse
    {
        return ApiResponse::success(
            new UserResource($request->user()),
            'User profile retrieved successfully'
        );
    }

    /**
     * Revoke the current access token on logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logged out successfully');
    }
}
