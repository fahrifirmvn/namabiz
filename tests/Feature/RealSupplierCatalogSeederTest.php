<?php

namespace Tests\Feature;

use App\Models\IndustryCategory;
use App\Models\Supplier;
use Database\Seeders\SupplierSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RealSupplierCatalogSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_seeder_populates_idempotent_real_indonesian_companies_across_all_categories(): void
    {
        Storage::fake('public');

        $this->seed(SupplierSeeder::class);
        $this->seed(SupplierSeeder::class);

        $this->assertDatabaseCount('suppliers', 15);
        $this->assertDatabaseCount('documents', 0);

        $categories = IndustryCategory::pluck('name')->all();
        $seededCategories = Supplier::pluck('industry_category')->unique()->all();

        foreach ($categories as $category) {
            $this->assertContains($category, $seededCategories);
        }

        foreach (Supplier::with('tags')->get() as $supplier) {
            $this->assertFalse(str_starts_with($supplier->slug, 'contoh-'));
            $this->assertStringNotContainsStringIgnoringCase('contoh', $supplier->name);
            $this->assertStringNotContainsStringIgnoringCase('demo', $supplier->name);
            $this->assertStringNotContainsString('Profil fiktif', (string) $supplier->description);
            $this->assertSame('Indonesia', $supplier->country);
            $this->assertFalse($supplier->is_verified);
            $this->assertNull($supplier->whatsapp);
            $this->assertNull($supplier->production_capacity);
            $this->assertNull($supplier->moq);
            $this->assertNull($supplier->avg_lead_time);
            $this->assertNotNull($supplier->founded_year);
            $this->assertStringStartsWith('https://', (string) $supplier->website);
            $this->assertNotNull($supplier->logo);
            Storage::disk('public')->assertExists($supplier->logo);
            $this->assertGreaterThan(0, $supplier->tags->count());
        }
    }
}
