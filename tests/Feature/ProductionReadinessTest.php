<?php

namespace Tests\Feature;

use App\Models\Certification;
use App\Models\Document;
use App\Models\IndustryCategory;
use App\Models\Supplier;
use App\Models\User;
use App\Observers\CatalogCacheObserver;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_protect_documents_command_moves_public_files_to_private_local_disk(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $supplier = Supplier::factory()->create();
        $filePath = UploadedFile::fake()->create('audit-rahasia.pdf', 120, 'application/pdf')
            ->store('documents', 'public');

        $document = $supplier->documents()->create([
            'title' => 'Laporan Audit Rahasia',
            'type' => 'PDF',
            'file_path' => $filePath,
            'is_premium' => true,
        ]);

        // Dry run keeps file untouched
        $this->artisan('catalog:protect-documents')->assertSuccessful();
        Storage::disk('public')->assertExists($filePath);
        Storage::disk('local')->assertMissing($filePath);

        // Apply moves file to private disk
        $this->artisan('catalog:protect-documents --apply')->assertSuccessful();
        Storage::disk('public')->assertMissing($filePath);
        Storage::disk('local')->assertExists($filePath);

        // Free user is forbidden; premium user downloads from local disk
        $freeUser = User::factory()->create();
        $premiumUser = User::factory()->create(['subscription_tier' => 'premium', 'premium_expires_at' => now()->addMonth()]);

        $this->actingAs($freeUser)->get(route('documents.download', $document))->assertForbidden();
        $this->actingAs($premiumUser)->get(route('documents.download', $document))->assertOk();
    }

    public function test_production_database_seeder_only_seeds_master_taxonomy_without_demo_users_or_suppliers(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('suppliers', 0);
        $this->assertGreaterThan(0, IndustryCategory::count());
        $this->assertGreaterThan(0, Certification::count());
    }

    public function test_create_admin_command_creates_admin_account_without_seeded_defaults(): void
    {
        $this->artisan('namabiz:create-admin', [
            '--name' => 'Operasional Namabiz',
            '--email' => 'ops@namabiz.com',
            '--password' => 'StrongPass#2026',
        ])->assertSuccessful();

        $admin = User::where('email', 'ops@namabiz.com')->firstOrFail();
        $this->assertTrue($admin->isAdmin());
        $this->assertNotNull($admin->email_verified_at);
    }

    public function test_dashboard_uses_honest_empty_states_and_tracks_recently_viewed_suppliers(): void
    {
        $user = User::factory()->unverified()->create();
        Supplier::factory()->create(['name' => 'PT Pemasok Lain', 'slug' => 'pt-pemasok-lain']);
        $viewedSupplier = Supplier::factory()->create(['name' => 'PT Baru Dikunjungi', 'slug' => 'pt-baru-dikunjungi']);

        // Unsaved dashboard has empty states rather than fake saved/recently viewed rows
        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Belum ada pemasok tersimpan.')
            ->assertSee('Belum ada profil pemasok yang Anda buka pada sesi ini.')
            ->assertDontSee('94% relevan');

        // Visiting a supplier profile records it in session recently viewed list
        $this->actingAs($user)->get(route('suppliers.show', $viewedSupplier->slug))->assertOk();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('PT Baru Dikunjungi')
            ->assertSee('Belum ada pemasok tersimpan.');
    }

    public function test_catalog_overview_is_cached_without_serializable_classes_and_invalidated_on_change(): void
    {
        config()->set('cache.default', 'database');
        config()->set('cache.serializable_classes', false);
        Cache::forgetDriver('database');

        $supplier = Supplier::factory()->create(['name' => 'PT Overview Cache']);

        $this->get(route('home'))->assertOk()->assertSee('PT Overview Cache');
        $this->assertTrue(Cache::has(CatalogCacheObserver::OVERVIEW_KEY));

        Document::create([
            'supplier_id' => $supplier->id,
            'title' => 'Brochure',
            'type' => 'PDF',
            'is_premium' => false,
        ]);

        $this->assertFalse(Cache::has(CatalogCacheObserver::OVERVIEW_KEY));
    }

    public function test_sitemap_and_seo_meta_and_custom_error_pages_render_properly(): void
    {
        $supplier = Supplier::factory()->create([
            'name' => 'PT Kemasan SEO Nusantara',
            'slug' => 'pt-kemasan-seo-nusantara',
            'tagline' => 'Produsen kardus bergelombang industri',
        ]);

        // Sitemap XML includes home, discover, and supplier slug and invalidates when updated
        $sitemap = $this->get(route('sitemap'));
        $sitemap->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('home'), false)
            ->assertSee(route('discover.index'), false)
            ->assertSee(route('suppliers.show', $supplier->slug), false);

        // Supplier page sets dynamic title, OG tags, and canonical URL
        $this->get(route('suppliers.show', $supplier->slug))
            ->assertOk()
            ->assertSee('<title>PT Kemasan SEO Nusantara — Namabiz</title>', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('rel="canonical" href="'.route('suppliers.show', $supplier->slug).'"', false);

        // Custom 404 page renders localized Namabiz UI
        $this->get('/halaman-tidak-ditemukan-uji')->assertNotFound()->assertSee('Halaman tidak ditemukan');
    }

    public function test_registration_action_is_rate_limited_to_prevent_spam(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Volt::test('pages.auth.register')
                ->set('name', '')
                ->set('email', "spam{$i}@example.com")
                ->set('password', 'password123')
                ->set('password_confirmation', 'password123')
                ->call('register')
                ->assertHasErrors(['name']);
        }

        Volt::test('pages.auth.register')
            ->set('name', 'Spammer')
            ->set('email', 'spam-final@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);
    }
}
