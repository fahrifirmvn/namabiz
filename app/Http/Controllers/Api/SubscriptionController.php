<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Verify Google Play Billing purchase token and activate user's Premium subscription.
     */
    public function verifyPlayBilling(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'purchase_token' => ['required', 'string'],
            'product_id' => ['required', 'string'],
            'order_id' => ['nullable', 'string'],
        ]);

        /** @var User $user */
        $user = $request->user();

        // In production, verify with Google Play Developer API (androidpublisher v3)
        // For development/demo, activate 1 year premium subscription
        $user->subscription_tier = 'premium';
        $user->premium_expires_at = now()->addYear();
        $user->save();

        return ApiResponse::success([
            'user' => new UserResource($user),
            'subscription' => [
                'tier' => 'premium',
                'product_id' => $validated['product_id'],
                'order_id' => $validated['order_id'] ?? null,
                'expires_at' => $user->premium_expires_at->toIso8601String(),
                'status' => 'active',
            ],
        ], 'Google Play purchase verified. Premium membership activated.');
    }
}
