<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_verify_play_billing_returns_503_when_verification_is_disabled(): void
    {
        $user = User::factory()->create([
            'subscription_tier' => 'free',
            'premium_expires_at' => null,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.subscription.verify-play-billing'), [
                'purchase_token' => 'play-purchase-token-xyz-12345',
                'product_id' => 'supplychain_premium_yearly',
                'order_id' => 'GPA.1234-5678-9012-34567',
            ]);

        $response->assertStatus(503)
            ->assertJson([
                'success' => false,
                'message' => 'Google Play Billing verification is not enabled.',
            ]);

        $user->refresh();
        $this->assertEquals('free', $user->subscription_tier);
        $this->assertNull($user->premium_expires_at);
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
