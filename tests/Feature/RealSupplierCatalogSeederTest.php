<?php

namespace Tests\Feature;

use App\Models\Certification;
use App\Models\IndustryCategory;
use App\Models\Supplier;
use Database\Seeders\SupplierSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RealSupplierCatalogSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_seeder_populates_35_idempotent_real_indonesian_companies_with_media_and_balanced_categories(): void
    {
        Storage::fake('public');

        $this->seed(SupplierSeeder::class);
        $this->seed(SupplierSeeder::class);

        $this->assertDatabaseCount('suppliers', 35);
        $this->assertDatabaseCount('documents', 0);
        $this->assertDatabaseCount('certifications', 7);

        $categories = IndustryCategory::pluck('name')->all();
        foreach ($categories as $category) {
            $this->assertSame(
                5,
                Supplier::where('industry_category', $category)->count(),
                "Expected 5 curated suppliers in category {$category}"
            );
        }

        $this->assertTrue(Certification::where('name', 'Halal BPJPH / MUI')->exists());
        $this->assertTrue(Certification::where('name', 'ISO 45001')->exists());
        $this->assertTrue(Certification::where('name', 'ISO 22000')->exists());

        foreach (Supplier::with('tags')->get() as $supplier) {
            $this->assertFalse(str_starts_with($supplier->slug, 'contoh-'));
            $this->assertStringNotContainsStringIgnoringCase('contoh', $supplier->name);
            $this->assertStringNotContainsStringIgnoringCase('demo', $supplier->name);
            $this->assertStringNotContainsString('Profil fiktif', (string) $supplier->description);
            $this->assertSame('Indonesia', $supplier->country);
            $this->assertFalse($supplier->is_verified);
            $this->assertNull($supplier->moq);
            $this->assertNull($supplier->avg_lead_time);
            $this->assertStringStartsWith('https://', (string) $supplier->website);
            $this->assertNotNull($supplier->logo);
            $this->assertNotNull($supplier->cover_image);
            Storage::disk('public')->assertExists($supplier->logo);
            Storage::disk('public')->assertExists($supplier->cover_image);
            $this->assertGreaterThan(0, $supplier->tags->count());
        }
    }
}
