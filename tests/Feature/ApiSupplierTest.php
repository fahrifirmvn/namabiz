<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSupplierTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_suppliers_with_pagination(): void
    {
        Supplier::factory()->count(15)->create();

        $response = $this->getJson(route('api.suppliers.index', ['per_page' => 10]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Suppliers retrieved successfully',
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'name', 'slug', 'country', 'industry_category',
                        'is_verified', 'premium_data',
                    ],
                ],
                'meta' => [
                    'pagination' => ['current_page', 'last_page', 'per_page', 'total', 'has_more'],
                ],
            ])
            ->assertJsonPath('meta.pagination.per_page', 10)
            ->assertJsonPath('meta.pagination.total', 15)
            ->assertJsonPath('meta.pagination.has_more', true);
    }

    public function test_can_filter_suppliers_by_search_keyword(): void
    {
        Supplier::factory()->create([
            'name' => 'PT Bio Packaging Nusantara',
            'industry_category' => 'Packaging Manufacturer',
        ]);

        Supplier::factory()->create([
            'name' => 'CV Logam Abadi Metal',
            'industry_category' => 'Raw Material Supplier',
        ]);

        $response = $this->getJson(route('api.suppliers.index', ['q' => 'Packaging']));

        $response->assertStatus(200)
            ->assertJsonPath('meta.pagination.total', 1)
            ->assertJsonPath('data.0.name', 'PT Bio Packaging Nusantara');
    }

    public function test_can_filter_suppliers_by_country_and_category(): void
    {
        Supplier::factory()->create([
            'name' => 'Indo Spices Ltd',
            'country' => 'Indonesia',
            'industry_category' => 'Raw Material Supplier',
        ]);

        Supplier::factory()->create([
            'name' => 'Vietnam Agro Ltd',
            'country' => 'Vietnam',
            'industry_category' => 'Raw Material Supplier',
        ]);

        $response = $this->getJson(route('api.suppliers.index', [
            'country' => 'Indonesia',
            'category' => 'Raw Material Supplier',
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('meta.pagination.total', 1)
            ->assertJsonPath('data.0.name', 'Indo Spices Ltd');
    }

    public function test_can_view_supplier_details_by_slug_or_id(): void
    {
        $supplier = Supplier::factory()->create([
            'name' => 'Atlas Packaging',
            'slug' => 'atlas-packaging',
        ]);

        $response = $this->getJson(route('api.suppliers.show', ['idOrSlug' => 'atlas-packaging']));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $supplier->id,
                    'name' => 'Atlas Packaging',
                    'slug' => 'atlas-packaging',
                ],
            ]);
    }

    public function test_free_user_receives_masked_premium_fields(): void
    {
        $supplier = Supplier::factory()->create([
            'slug' => 'target-supplier',
            'founded_year' => 2005,
            'company_size' => '500-1000 employees',
            'production_capacity' => '5M units/mo',
            'moq' => '10,000 units',
            'avg_lead_time' => '4-6 weeks',
            'carbon_footprint_report' => 'Available (2024)',
        ]);

        $freeUser = User::factory()->create(['subscription_tier' => 'free']);

        // Request as authenticated Free user
        $response = $this->actingAs($freeUser, 'sanctum')
            ->getJson(route('api.suppliers.show', ['idOrSlug' => 'target-supplier']));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'premium_data' => [
                        'is_locked' => true,
                        'founded_year' => null,
                        'company_size' => null,
                        'production_capacity' => null,
                        'moq' => null,
                        'avg_lead_time' => null,
                        'carbon_footprint_report' => null,
                    ],
                ],
            ]);

        // Request as Guest (unauthenticated) also masked
        $guestResponse = $this->getJson(route('api.suppliers.show', ['idOrSlug' => 'target-supplier']));

        $guestResponse->assertStatus(200)
            ->assertJson([
                'data' => [
                    'premium_data' => [
                        'is_locked' => true,
                        'production_capacity' => null,
                    ],
                ],
            ]);
    }

    public function test_premium_user_receives_unmasked_real_premium_data(): void
    {
        $supplier = Supplier::factory()->create([
            'slug' => 'premium-target',
            'founded_year' => 2010,
            'company_size' => '100-500 employees',
            'production_capacity' => '3M units/mo',
            'moq' => '5,000 units',
            'avg_lead_time' => '2-3 weeks',
            'carbon_footprint_report' => 'ISO 14064 Verified',
        ]);

        $premiumUser = User::factory()->premium()->create();

        $response = $this->actingAs($premiumUser, 'sanctum')
            ->getJson(route('api.suppliers.show', ['idOrSlug' => 'premium-target']));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'premium_data' => [
                        'is_locked' => false,
                        'founded_year' => 2010,
                        'company_size' => '100-500 employees',
                        'production_capacity' => '3M units/mo',
                        'moq' => '5,000 units',
                        'avg_lead_time' => '2-3 weeks',
                        'carbon_footprint_report' => 'ISO 14064 Verified',
                    ],
                ],
            ]);
    }

    public function test_qr_resolve_endpoint_resolves_url_or_slug(): void
    {
        $supplier = Supplier::factory()->create([
            'name' => 'Booth Supplier Expo',
            'slug' => 'booth-supplier-expo',
        ]);

        // Scenario 1: Scan full website URL from QR
        $urlResponse = $this->getJson(route('api.suppliers.qr-resolve', [
            'code' => 'https://supplychainpro.id/suppliers/booth-supplier-expo',
        ]));

        $urlResponse->assertStatus(200)
            ->assertJsonPath('data.id', $supplier->id)
            ->assertJsonPath('data.name', 'Booth Supplier Expo');

        // Scenario 2: Scan deep link scheme from QR
        $deepLinkResponse = $this->getJson(route('api.suppliers.qr-resolve', [
            'code' => 'supplychainpro://supplier/booth-supplier-expo',
        ]));

        $deepLinkResponse->assertStatus(200)
            ->assertJsonPath('data.id', $supplier->id);

        // Scenario 3: Scan raw slug/ID directly
        $rawResponse = $this->getJson(route('api.suppliers.qr-resolve', [
            'code' => 'booth-supplier-expo',
        ]));

        $rawResponse->assertStatus(200)
            ->assertJsonPath('data.id', $supplier->id);
    }

    public function test_qr_resolve_returns_404_for_unknown_code(): void
    {
        $response = $this->getJson(route('api.suppliers.qr-resolve', [
            'code' => 'non-existent-booth-999',
        ]));

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_authenticated_user_can_toggle_save_supplier(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();

        // 1. Toggle save -> Saved
        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.suppliers.toggle-save', ['id' => $supplier->id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'is_saved' => true,
                    'supplier_id' => $supplier->id,
                ],
            ]);

        $this->assertDatabaseHas('saved_suppliers', [
            'user_id' => $user->id,
            'supplier_id' => $supplier->id,
        ]);

        // 2. Toggle save again -> Removed
        $secondResponse = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.suppliers.toggle-save', ['id' => $supplier->id]));

        $secondResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'is_saved' => false,
                ],
            ]);

        $this->assertDatabaseMissing('saved_suppliers', [
            'user_id' => $user->id,
            'supplier_id' => $supplier->id,
        ]);
    }

    public function test_authenticated_user_can_view_saved_suppliers_collection(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create(['name' => 'Favorited Supplier']);

        $user->savedSuppliers()->create(['supplier_id' => $supplier->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson(route('api.suppliers.saved'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    [
                        'id' => $supplier->id,
                        'name' => 'Favorited Supplier',
                        'is_saved' => true,
                    ],
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_save_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->postJson(route('api.suppliers.toggle-save', ['id' => $supplier->id]));

        $response->assertStatus(401);
    }
}
