<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiComparisonTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_compare_two_suppliers_side_by_side(): void
    {
        $supplier1 = Supplier::factory()->create(['name' => 'Supplier Alpha']);
        $supplier2 = Supplier::factory()->create(['name' => 'Supplier Beta']);

        $response = $this->postJson(route('api.compare'), [
            'supplier_ids' => [$supplier1->id, $supplier2->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Suppliers comparison generated successfully',
                'data' => [
                    'compared_count' => 2,
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'suppliers' => [
                        '*' => ['id', 'name', 'slug', 'premium_data'],
                    ],
                    'compared_count',
                ],
            ]);
    }

    public function test_cannot_compare_less_than_two_suppliers(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->postJson(route('api.compare'), [
            'supplier_ids' => [$supplier->id],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['supplier_ids']);
    }

    public function test_free_user_receives_masked_data_in_comparison(): void
    {
        $supplier1 = Supplier::factory()->create(['production_capacity' => '5M units/mo']);
        $supplier2 = Supplier::factory()->create(['production_capacity' => '10M units/mo']);

        $freeUser = User::factory()->create(['subscription_tier' => 'free']);

        $response = $this->actingAs($freeUser, 'sanctum')
            ->postJson(route('api.compare'), [
                'supplier_ids' => [$supplier1->id, $supplier2->id],
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.suppliers.0.premium_data.is_locked', true)
            ->assertJsonPath('data.suppliers.0.premium_data.production_capacity', null)
            ->assertJsonPath('data.suppliers.1.premium_data.is_locked', true)
            ->assertJsonPath('data.suppliers.1.premium_data.production_capacity', null);
    }

    public function test_premium_user_receives_unmasked_data_in_comparison(): void
    {
        $supplier1 = Supplier::factory()->create(['production_capacity' => '5M units/mo']);
        $supplier2 = Supplier::factory()->create(['production_capacity' => '10M units/mo']);

        $premiumUser = User::factory()->premium()->create();

        $response = $this->actingAs($premiumUser, 'sanctum')
            ->postJson(route('api.compare'), [
                'supplier_ids' => [$supplier1->id, $supplier2->id],
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.suppliers.0.premium_data.is_locked', false)
            ->assertJsonPath('data.suppliers.0.premium_data.production_capacity', '5M units/mo')
            ->assertJsonPath('data.suppliers.1.premium_data.is_locked', false)
            ->assertJsonPath('data.suppliers.1.premium_data.production_capacity', '10M units/mo');
    }

    public function test_premium_user_can_download_comparison_pdf(): void
    {
        $supplier1 = Supplier::factory()->create();
        $supplier2 = Supplier::factory()->create();

        $premiumUser = User::factory()->premium()->create();

        $response = $this->actingAs($premiumUser, 'sanctum')
            ->postJson(route('api.compare.pdf'), [
                'supplier_ids' => [$supplier1->id, $supplier2->id],
            ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_free_user_cannot_download_comparison_pdf(): void
    {
        $supplier1 = Supplier::factory()->create();
        $supplier2 = Supplier::factory()->create();

        $freeUser = User::factory()->create(['subscription_tier' => 'free']);

        $response = $this->actingAs($freeUser, 'sanctum')
            ->postJson(route('api.compare.pdf'), [
                'supplier_ids' => [$supplier1->id, $supplier2->id],
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'This action requires a Premium subscription.',
                'paywall' => true,
            ]);
    }
}
