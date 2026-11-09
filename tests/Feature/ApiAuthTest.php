<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_via_api_and_receives_token(): void
    {
        $response = $this->postJson(route('api.auth.register'), [
            'name' => 'Fahri Developer',
            'email' => 'fahri@example.com',
            'password' => 'secret1234',
            'device_name' => 'Pixel 8 Pro',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'user' => [
                        'name' => 'Fahri Developer',
                        'email' => 'fahri@example.com',
                        'subscription_tier' => 'free',
                        'is_premium' => false,
                    ],
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email', 'subscription_tier', 'is_premium'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'fahri@example.com',
        ]);
    }

    public function test_user_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->postJson(route('api.auth.register'), [
            'name' => 'Duplicate User',
            'email' => 'duplicate@example.com',
            'password' => 'secret1234',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login_via_api_and_receives_token(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson(route('api.auth.login'), [
            'email' => 'john@example.com',
            'password' => 'password123',
            'device_name' => 'Samsung S24',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'email' => 'john@example.com',
                    ],
                ],
            ])
            ->assertJsonStructure([
                'data' => ['token', 'user'],
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'wrong@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->postJson(route('api.auth.login'), [
            'email' => 'wrong@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid email or password credentials.',
            ]);
    }

    public function test_user_can_authenticate_via_google_endpoint(): void
    {
        $response = $this->postJson(route('api.auth.google'), [
            'email' => 'googleuser@gmail.com',
            'name' => 'Google User',
            'avatar' => 'https://lh3.googleusercontent.com/a/default-user',
            'device_name' => 'Pixel 7',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'email' => 'googleuser@gmail.com',
                        'name' => 'Google User',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'googleuser@gmail.com',
        ]);
    }

    public function test_authenticated_user_can_retrieve_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson(route('api.auth.user'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_unauthenticated_request_to_profile_is_unauthorized(): void
    {
        $response = $this->getJson(route('api.auth.user'));

        $response->assertStatus(401);
    }

    public function test_user_can_logout_and_token_is_revoked(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-device')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(route('api.auth.logout'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logged out successfully',
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
