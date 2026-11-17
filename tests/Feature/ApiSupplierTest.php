<?php

namespace Tests\Feature;

use App\Models\Certification;
use App\Models\IndustryCategory;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_api_filters_returns_categories_countries_and_certifications(): void
    {
        IndustryCategory::create([
            'name' => 'Spices & Coconut',
            'slug' => 'spices-coconut',
        ]);
        Supplier::factory()->create([
            'country' => 'Indonesia',
            'industry_category' => 'Spices & Coconut',
            'whatsapp' => '081234567890',
        ]);
        Certification::create([
            'name' => 'Halal MUI',
            'category' => 'Compliance',
        ]);

        $this->getJson(route('api.filters'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.categories.0.name', 'Spices & Coconut')
            ->assertJsonPath('data.categories.0.slug', 'spices-coconut')
            ->assertJsonPath('data.countries.0.country', 'Indonesia')
            ->assertJsonPath('data.certifications.0.name', 'Halal MUI');
    }

    public function test_supplier_resource_exposes_whatsapp_and_supports_slug_category_and_name_sort(): void
    {
        IndustryCategory::create([
            'name' => 'Packaging Manufacturer',
            'slug' => 'packaging-manufacturer',
        ]);
        Supplier::factory()->create([
            'name' => 'Zeta Pack',
            'industry_category' => 'Packaging Manufacturer',
            'whatsapp' => '081122334455',
            'email' => 'sales@zetapack.test',
        ]);
        Supplier::factory()->create([
            'name' => 'Alpha Pack',
            'industry_category' => 'Packaging Manufacturer',
            'whatsapp' => '6289988776655',
        ]);

        $response = $this->getJson(route('api.suppliers.index', [
            'category' => 'packaging-manufacturer',
            'sort_by' => 'name',
        ]));

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Alpha Pack')
            ->assertJsonPath('data.1.name', 'Zeta Pack')
            ->assertJsonPath('data.1.whatsapp', '081122334455')
            ->assertJsonPath('data.1.whatsapp_url', 'https://wa.me/6281122334455?text=Halo%20Zeta%20Pack%2C%20saya%20menemukan%20profil%20perusahaan%20Anda%20di%20platform%20Namabiz.')
            ->assertJsonPath('data.1.email', 'sales@zetapack.test');
    }

    public function test_dashboard_home_reflects_genuine_guest_free_and_premium_state(): void
    {
        $supplier = Supplier::factory()->create(['slug' => 'pt-maju-jaya', 'whatsapp' => '08123456789']);

        // Guest sees Guest tier and zero fake counts
        $this->getJson(route('api.dashboard.home'))
            ->assertOk()
            ->assertJsonPath('data.membership.tier', 'Guest')
            ->assertJsonPath('data.membership.is_active', false)
            ->assertJsonPath('data.quick_stats.saved', 0)
            ->assertJsonPath('data.quick_stats.comparing', 0)
            ->assertJsonPath('data.needs_attention.0.supplier_slug', 'pt-maju-jaya');

        // Free user with 1 saved supplier sees real saved count and Free tier
        $freeUser = User::factory()->create(['subscription_tier' => 'free']);
        $freeUser->savedSuppliers()->create(['supplier_id' => $supplier->id]);

        $this->actingAs($freeUser, 'sanctum')
            ->getJson(route('api.dashboard.home'))
            ->assertOk()
            ->assertJsonPath('data.membership.tier', 'Free')
            ->assertJsonPath('data.membership.is_active', false)
            ->assertJsonPath('data.quick_stats.saved', 1);

        // Premium user sees Business Pro active status
        $premiumUser = User::factory()->create([
            'subscription_tier' => 'premium',
            'premium_expires_at' => now()->addMonths(6),
        ]);

        $this->actingAs($premiumUser, 'sanctum')
            ->getJson(route('api.dashboard.home'))
            ->assertOk()
            ->assertJsonPath('data.membership.tier', 'Business Pro')
            ->assertJsonPath('data.membership.is_active', true);
    }

    public function test_authenticated_supplier_listing_avoids_n_plus_one_saved_queries_and_versions_local_logo(): void
    {
        $user = User::factory()->create();
        $saved = Supplier::factory()->create([
            'logo' => 'suppliers/brand.webp',
        ]);
        $user->savedSuppliers()->create(['supplier_id' => $saved->id]);

        DB::flushQueryLog();
        DB::enableQueryLog();
        $firstResponse = $this->actingAs($user, 'sanctum')->getJson(route('api.suppliers.index', ['per_page' => 20]));
        $fewQueries = count(DB::getQueryLog());
        DB::disableQueryLog();

        $firstResponse->assertOk()
            ->assertJsonPath('data.0.is_saved', true);
        $this->assertStringContainsString(
            '/storage/suppliers/brand.webp?v=',
            (string) $firstResponse->json('data.0.logo')
        );

        Supplier::factory()->count(8)->create();

        DB::flushQueryLog();
        DB::enableQueryLog();
        $secondResponse = $this->actingAs($user, 'sanctum')->getJson(route('api.suppliers.index', ['per_page' => 20]));
        $manyQueries = count(DB::getQueryLog());
        DB::disableQueryLog();

        $secondResponse->assertOk()
            ->assertJsonPath('meta.pagination.total', 9);
        $this->assertSame($fewQueries, $manyQueries);
    }

    public function test_filters_and_suppliers_support_etag_304_and_invalidate_on_changes(): void
    {
        Supplier::factory()->create([
            'name' => 'PT Katalog Cepat',
            'slug' => 'pt-katalog-cepat',
            'country' => 'Indonesia',
            'industry_category' => 'Spices',
        ]);

        // 1. Filters endpoint exposes public max-age=120 and returns 304 on matching If-None-Match
        $filtersResponse = $this->getJson(route('api.filters'));
        $filtersResponse->assertOk()
            ->assertHeader('Cache-Control', 'max-age=120, public');
        $filtersEtag = (string) $filtersResponse->headers->get('ETag');
        $this->assertNotEmpty($filtersEtag);

        $filtersNotModified = $this->withHeaders(['If-None-Match' => $filtersEtag])
            ->getJson(route('api.filters'));
        $filtersNotModified->assertStatus(304);

        // 2. Suppliers listing exposes private, no-cache, must-revalidate + Vary: Authorization + 304
        $guestList = $this->getJson(route('api.suppliers.index', ['per_page' => 10]));
        $guestList->assertOk();
        $listCacheControl = (string) $guestList->headers->get('Cache-Control');
        $this->assertStringContainsString('private', $listCacheControl);
        $this->assertStringContainsString('must-revalidate', $listCacheControl);
        $this->assertStringContainsString('no-cache', $listCacheControl);
        $this->assertStringContainsString('Authorization', (string) $guestList->headers->get('Vary'));

        $listEtag = (string) $guestList->headers->get('ETag');
        $this->assertNotEmpty($listEtag);

        $listNotModified = $this->withHeaders(['If-None-Match' => $listEtag])
            ->getJson(route('api.suppliers.index', ['per_page' => 10]));
        $listNotModified->assertStatus(304);

        // 3. Supplier detail produces distinct ETags across users/save-states and avoids stale cross-user 304
        $userA = User::factory()->create(['subscription_tier' => 'free']);
        $userB = User::factory()->premium()->create();

        $detailA = $this->actingAs($userA, 'sanctum')
            ->getJson(route('api.suppliers.show', ['idOrSlug' => 'pt-katalog-cepat']));
        $detailA->assertOk();
        $etagA = (string) $detailA->headers->get('ETag');

        $detailB = $this->actingAs($userB, 'sanctum')
            ->withHeaders(['If-None-Match' => $etagA])
            ->getJson(route('api.suppliers.show', ['idOrSlug' => 'pt-katalog-cepat']));
        $detailB->assertOk()
            ->assertJsonPath('data.premium_data.is_locked', false);
        $this->assertNotSame($etagA, (string) $detailB->headers->get('ETag'));

        // Same user revalidates cleanly after bookmark toggle (ETag must change)
        $supplierId = (int) $detailA->json('data.id');
        $this->actingAs($userA, 'sanctum')
            ->postJson(route('api.suppliers.toggle-save', ['id' => $supplierId]))
            ->assertOk();

        $detailAAfterSave = $this->actingAs($userA, 'sanctum')
            ->withHeaders(['If-None-Match' => $etagA])
            ->getJson(route('api.suppliers.show', ['idOrSlug' => 'pt-katalog-cepat']));
        $detailAAfterSave->assertOk()
            ->assertJsonPath('data.is_saved', true);
    }

    public function test_dashboard_home_supports_conditional_etag_and_user_isolation(): void
    {
        Supplier::factory()->create(['slug' => 'pt-beranda']);

        $guestHome = $this->getJson(route('api.dashboard.home'));
        $guestHome->assertOk();
        $cacheControl = (string) $guestHome->headers->get('Cache-Control');
        $this->assertStringContainsString('private', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
        $this->assertStringContainsString('Authorization', (string) $guestHome->headers->get('Vary'));

        $guestEtag = (string) $guestHome->headers->get('ETag');
        $this->assertNotEmpty($guestEtag);

        $this->withHeaders(['If-None-Match' => $guestEtag])
            ->getJson(route('api.dashboard.home'))
            ->assertStatus(304);

        $user = User::factory()->create(['subscription_tier' => 'free']);
        $userHome = $this->actingAs($user, 'sanctum')
            ->withHeaders(['If-None-Match' => $guestEtag])
            ->getJson(route('api.dashboard.home'));
        $userHome->assertOk()
            ->assertJsonPath('data.membership.tier', 'Free');
        $this->assertNotSame($guestEtag, (string) $userHome->headers->get('ETag'));
    }
}
