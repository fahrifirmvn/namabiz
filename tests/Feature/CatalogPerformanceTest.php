<?php

namespace Tests\Feature;

use App\Models\Certification;
use App\Models\Supplier;
use App\Models\SupplierReview;
use App\Observers\CatalogCacheObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CatalogPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private function countQueriesForDiscover(): int
    {
        Cache::flush();
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->get(route('discover.index', ['q' => 'a']))->assertOk();
        $count = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $count;
    }

    public function test_discover_query_count_does_not_grow_with_supplier_count(): void
    {
        Supplier::factory()->count(2)->create();
        $few = $this->countQueriesForDiscover();

        Supplier::factory()->count(10)->create()->each(
            fn (Supplier $supplier) => SupplierReview::factory()->count(2)->create(['supplier_id' => $supplier->id])
        );
        $many = $this->countQueriesForDiscover();

        $this->assertSame($few, $many);
    }

    public function test_filter_options_are_cached_and_invalidated_when_catalog_changes(): void
    {
        Supplier::factory()->create(['country' => 'Indonesia']);
        $this->get(route('discover.index'))->assertOk();
        $this->assertTrue(Cache::has(CatalogCacheObserver::FILTER_OPTIONS_KEY));

        Supplier::factory()->create(['country' => 'Vietnam']);
        $this->assertFalse(Cache::has(CatalogCacheObserver::FILTER_OPTIONS_KEY));

        Cache::put(CatalogCacheObserver::FILTER_OPTIONS_KEY, 'stale');
        Certification::factory()->create(['name' => 'ISO 9001', 'category' => 'Mutu']);
        $this->assertFalse(Cache::has(CatalogCacheObserver::FILTER_OPTIONS_KEY));
    }

    public function test_review_stats_match_review_rows(): void
    {
        $supplier = Supplier::factory()->create();
        SupplierReview::factory()->create(['supplier_id' => $supplier->id, 'rating' => 5, 'quality_rating' => 5, 'delivery_rating' => 4, 'service_rating' => 3]);
        SupplierReview::factory()->create(['supplier_id' => $supplier->id, 'rating' => 3, 'quality_rating' => 3, 'delivery_rating' => 4, 'service_rating' => 5]);

        $stats = $supplier->reviewStats();

        $this->assertSame(2, $stats['count']);
        $this->assertSame(4.0, $stats['average']);
        $this->assertSame(4.0, $stats['quality']);
        $this->assertSame(4.0, $stats['delivery']);
        $this->assertSame(4.0, $stats['service']);
        $this->assertSame(50, $stats['breakdown'][5]);
        $this->assertSame(50, $stats['breakdown'][3]);
        $this->assertSame(0, $stats['breakdown'][1]);
    }

    public function test_review_stats_are_empty_for_supplier_without_reviews(): void
    {
        $stats = Supplier::factory()->create()->reviewStats();

        $this->assertSame(0, $stats['count']);
        $this->assertNull($stats['average']);
        $this->assertSame(0, $stats['breakdown'][5]);
    }
}
