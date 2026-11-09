<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_verify_play_billing_purchase(): void
    {
        $user = User::factory()->create(['subscription_tier' => 'free']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.subscription.verify-play-billing'), [
                'purchase_token' => 'play-purchase-token-xyz-12345',
                'product_id' => 'supplychain_premium_yearly',
                'order_id' => 'GPA.1234-5678-9012-34567',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Google Play purchase verified. Premium membership activated.',
                'data' => [
                    'user' => [
                        'subscription_tier' => 'premium',
                        'is_premium' => true,
                    ],
                    'subscription' => [
                        'tier' => 'premium',
                        'product_id' => 'supplychain_premium_yearly',
                        'status' => 'active',
                    ],
                ],
            ]);

        $user->refresh();
        $this->assertEquals('premium', $user->subscription_tier);
        $this->assertNotNull($user->premium_expires_at);
        $this->assertTrue($user->premium_expires_at->isFuture());
    }

    public function test_unauthenticated_user_cannot_verify_purchase(): void
    {
        $response = $this->postJson(route('api.subscription.verify-play-billing'), [
            'purchase_token' => 'play-purchase-token-xyz-12345',
            'product_id' => 'supplychain_premium_yearly',
        ]);

        $response->assertStatus(401);
    }

    public function test_purchase_validation_fails_without_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.subscription.verify-play-billing'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['purchase_token', 'product_id']);
    }
}
