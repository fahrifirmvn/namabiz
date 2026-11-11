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
}
