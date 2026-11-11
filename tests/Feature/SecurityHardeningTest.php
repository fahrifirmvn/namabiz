<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_sec_001_api_supplier_update_requires_admin_authorization(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Original Factory Name', 'is_verified' => false]);
        $buyer = User::factory()->create(['is_admin' => false]);
        $admin = User::factory()->create(['is_admin' => true]);

        // Unauthenticated attempt is rejected (401)
        $this->putJson(route('api.suppliers.update', ['id' => $supplier->id]), [
            'name' => 'Unauthorized Rename',
            'is_verified' => true,
        ])->assertStatus(401);

        // Non-admin buyer attempt is rejected (403)
        $this->actingAs($buyer, 'sanctum')
            ->putJson(route('api.suppliers.update', ['id' => $supplier->id]), [
                'name' => 'Buyer Rename',
                'is_verified' => true,
            ])->assertStatus(403);

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Original Factory Name',
            'is_verified' => false,
        ]);

        // Admin can update with valid http/https URL
        $this->actingAs($admin, 'sanctum')
            ->putJson(route('api.suppliers.update', ['id' => $supplier->id]), [
                'name' => 'Verified Factory Name',
                'website' => 'https://factory.example.com',
                'is_verified' => true,
            ])->assertOk();
    }

    public function test_sec_002_google_auth_endpoint_blocks_admin_account_takeover(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@namabiz.com',
            'is_admin' => true,
        ]);

        $this->postJson(route('api.auth.google'), [
            'email' => $admin->email,
            'name' => 'Attacker',
        ])->assertStatus(403);
    }

    public function test_sec_004_user_model_prevents_is_admin_mass_assignment(): void
    {
        $user = User::create([
            'name' => 'Regular Buyer',
            'email' => 'buyer@example.com',
            'password' => 'password123',
            'is_admin' => true,
        ]);

        $this->assertFalse($user->fresh()->isAdmin());
    }
}
