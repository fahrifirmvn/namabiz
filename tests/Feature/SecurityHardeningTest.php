<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

    public function test_sec_003_document_download_masks_premium_external_url_and_blocks_unsafe_schemes(): void
    {
        $supplier = Supplier::factory()->create(['slug' => 'doc-sec-supplier']);
        $premiumDoc = $supplier->documents()->create([
            'title' => 'Audit Rahasia 2026',
            'type' => 'PDF',
            'external_url' => 'https://cdn.example.com/secret-audit.pdf',
            'is_premium' => true,
        ]);

        $unsafeDoc = $supplier->documents()->create([
            'title' => 'Unsafe Scheme Doc',
            'type' => 'PDF',
            'external_url' => 'javascript:alert(1)',
            'is_premium' => false,
        ]);

        // Free/Guest API call must NOT leak external_url of a premium document
        $this->getJson(route('api.suppliers.show', ['idOrSlug' => $supplier->slug]))
            ->assertOk()
            ->assertJsonPath('data.documents.0.can_download', false)
            ->assertJsonPath('data.documents.0.external_url', null);

        // Unsafe javascript: scheme in external_url is blocked on download
        $this->get(route('documents.download', $unsafeDoc))->assertStatus(404);
        $this->getJson(route('api.documents.download', ['document' => $unsafeDoc->id]))->assertStatus(404);
    }

    public function test_sec_006_rejects_javascript_scheme_urls_and_svg_uploads(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $svgFile = UploadedFile::fake()->createWithContent(
            'malicious.svg',
            '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"></svg>'
        );

        $this->actingAs($admin)
            ->post(route('admin.suppliers.store'), [
                'name' => 'PT Uji Skema',
                'country' => 'Indonesia',
                'industry_category' => 'Packaging Manufacturer',
                'website' => 'javascript:alert(document.cookie)',
                'logo_url' => 'data:text/html,<script>alert(1)</script>',
                'logo_file' => $svgFile,
            ])
            ->assertSessionHasErrors(['website', 'logo_url', 'logo_file']);

        $supplier = Supplier::factory()->make([
            'website' => 'javascript:alert(1)',
            'whatsapp' => 'abc',
        ]);
        $this->assertNull($supplier->safe_website_url);
        $this->assertNull($supplier->whatsapp_url);
    }

    public function test_sec_007_parameterized_search_handles_sql_metacharacters_and_wildcards_safely(): void
    {
        Supplier::factory()->create(['name' => 'PT Baja Murni Nusantara']);

        foreach (["' OR '1'='1", '; DROP TABLE suppliers; --', 'UNION SELECT NULL', '%%%%%'] as $input) {
            $this->getJson(route('api.suppliers.index', ['q' => $input]))
                ->assertOk()
                ->assertJsonPath('meta.pagination.total', 0);
        }

        // Negative or excessive per_page is clamped between 1 and 50
        $this->getJson(route('api.suppliers.index', ['per_page' => -10]))
            ->assertOk()
            ->assertJsonPath('meta.pagination.per_page', 1);

        $this->getJson(route('api.suppliers.index', ['per_page' => 9999]))
            ->assertOk()
            ->assertJsonPath('meta.pagination.per_page', 50);
    }
}
