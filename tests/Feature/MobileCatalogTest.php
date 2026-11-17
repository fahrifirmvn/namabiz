<?php

namespace Tests\Feature;

use App\Livewire\DiscoverSearch;
use App\Models\Certification;
use App\Models\IndustryCategory;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class MobileCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_and_login_share_actual_stats_and_supplier_metrics(): void
    {
        Supplier::factory()->create([
            'name' => 'PT Kemasan Bersama', 'industry_category' => 'Packaging Manufacturer',
            'production_capacity' => '2.5M units/mo', 'moq' => '10,000 units',
            'avg_lead_time' => '4-6 weeks', 'is_verified' => true,
        ]);
        IndustryCategory::create(['name' => 'Kategori Kosong', 'slug' => 'kategori-kosong']);

        foreach (['/', '/login'] as $path) {
            $this->get($path)
                ->assertSee('PT Kemasan Bersama')
                ->assertSee('2,5 juta unit/bln')
                ->assertSee('10.000 unit')
                ->assertSee('4–6 minggu')
                ->assertDontSee('12.400+')
                ->assertDontSee('Kategori Kosong');
        }

        $this->get('/')->assertViewHas('catalogStats', ['suppliers' => 1, 'verified' => 1, 'categories' => 1]);
    }

    public function test_category_order_and_labels_are_consistent_and_empty_categories_are_hidden(): void
    {
        Supplier::factory()->count(2)->create(['industry_category' => 'Raw Material Supplier']);
        Supplier::factory()->create(['industry_category' => 'Contract Manufacturer']);
        IndustryCategory::create(['name' => 'Empty Category', 'slug' => 'empty']);

        foreach (['/', '/discover'] as $path) {
            $this->get($path)
                ->assertSeeInOrder(['Pemasok Bahan Baku', 'Produsen Kontrak'])
                ->assertDontSee('Empty Category');
        }
    }

    public function test_catalog_filters_by_category_city_and_certification_and_can_reset(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'PT Pilihan', 'city' => 'Bekasi', 'industry_category' => 'Packaging Manufacturer']);
        Supplier::factory()->create(['name' => 'PT Lainnya', 'city' => 'Bandung', 'industry_category' => 'Contract Manufacturer']);
        $certification = Certification::factory()->create(['name' => 'ISO 9001', 'category' => 'Mutu']);
        $supplier->certifications()->create(['certification_id' => $certification->id, 'status' => 'verified', 'is_premium_field' => false]);

        Livewire::test(DiscoverSearch::class)
            ->set('categories', ['Packaging Manufacturer'])
            ->set('locations', ['Bekasi'])
            ->set('certifications', ['ISO 9001'])
            ->call('applyFilters')
            ->assertSee('PT Pilihan')
            ->assertDontSee('PT Lainnya')
            ->call('removeFilter', 'locations', 'Bekasi')
            ->assertSet('locations', [])
            ->call('clearFilters')
            ->assertSee('PT Lainnya');
    }

    public function test_catalog_sorting_and_empty_results_work_without_hiding_controls(): void
    {
        Supplier::factory()->create(['name' => 'PT Awal', 'created_at' => now()->subDay()]);
        Supplier::factory()->create(['name' => 'PT Baru', 'created_at' => now()]);

        Livewire::test(DiscoverSearch::class)
            ->set('sort', 'newest')
            ->call('applyFilters')
            ->assertSeeInOrder(['PT Baru', 'PT Awal'])
            ->set('query', 'tidak-ada-pemasok')
            ->assertSee('Tidak ada pemasok untuk filter ini')
            ->assertSee('Filter')
            ->assertSee('Reset filter');
    }

    public function test_compare_toggle_has_one_label_and_enforces_the_existing_four_supplier_limit(): void
    {
        $suppliers = Supplier::factory()->count(5)->create();
        $component = Livewire::test(DiscoverSearch::class)->assertDontSee('+ Tambah ke perbandingan');

        foreach ($suppliers as $supplier) {
            $component->call('toggleCompare', $supplier->id);
        }

        $component->assertSet('compareIds', $suppliers->take(4)->pluck('id')->all())
            ->assertSee('Ditambahkan')
            ->assertSee('4 dipilih')
            ->call('toggleCompare', $suppliers->first()->id)
            ->assertSet('compareIds', $suppliers->slice(1, 3)->pluck('id')->all());
    }

    public function test_supplier_cards_do_not_claim_missing_contact_or_response_data(): void
    {
        Supplier::factory()->create([
            'name' => 'PT Profil Jujur', 'is_verified' => false, 'whatsapp' => null,
            'email' => null, 'production_capacity' => null, 'moq' => null, 'avg_lead_time' => null,
        ]);

        $this->get('/discover')->assertSee('Belum tersedia')
            ->assertDontSee('Respons:')
            ->assertDontSee('berkas tervalidasi')
            ->assertDontSee('info@namabiz.com')
            ->assertDontSee('Untuk Anda');
    }

    public function test_bottom_navigation_only_appears_for_signed_in_users(): void
    {
        $this->get('/discover')->assertDontSee('aria-label="Navigasi akun"', false);
        $this->actingAs(User::factory()->create())->get('/discover')
            ->assertSee('aria-label="Navigasi akun"', false);
    }

    #[TestWith(['10,000 units', 1000.0])]
    #[TestWith([null, null])]
    public function test_preview_only_highlights_a_comparable_moq(?string $otherMoq, ?float $lowestMoq): void
    {
        Supplier::factory()->create(['name' => 'PT MOQ Satu', 'moq' => '1,000 units']);
        Supplier::factory()->create(['name' => 'PT MOQ Dua', 'moq' => $otherMoq]);

        $this->get('/')->assertViewHas('lowestPreviewMoq', $lowestMoq);
    }

    public function test_operational_metrics_keep_the_same_format_on_profile_and_comparison_pages(): void
    {
        $supplier = Supplier::factory()->create([
            'production_capacity' => '2.5M units/mo', 'moq' => '10,000 units', 'avg_lead_time' => '4-6 weeks',
        ]);
        $user = User::factory()->create(['subscription_tier' => 'premium', 'premium_expires_at' => now()->addYear()]);
        $this->actingAs($user);

        foreach ([route('suppliers.show', $supplier->slug), route('compare.index', ['suppliers' => [$supplier->id]])] as $path) {
            $this->get($path)->assertSee('2,5 juta unit/bln')->assertSee('10.000 unit')->assertSee('4–6 minggu');
        }
    }

    public function test_catalog_pagination_is_localized_and_filter_reset_returns_to_the_first_page(): void
    {
        for ($index = 1; $index <= 13; $index++) {
            Supplier::factory()->create(['name' => sprintf('PT Pemasok %02d', $index), 'is_verified' => true]);
        }

        Livewire::test(DiscoverSearch::class)
            ->assertSee('Halaman 1 dari 2')
            ->assertSee('Berikutnya')
            ->assertDontSee('Showing')
            ->call('nextPage')
            ->assertSee('PT Pemasok 13')
            ->assertDontSee('PT Pemasok 01')
            ->call('clearFilters')
            ->assertSee('PT Pemasok 01')
            ->assertSee('Halaman 1 dari 2');
    }
}
