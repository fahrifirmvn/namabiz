<?php

namespace App\Observers;

use App\Models\Certification;
use App\Models\IndustryCategory;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CatalogCacheObserver
{
    public const FILTER_OPTIONS_KEY = 'discover.filter_options.v2';

    public const RECOMMENDED_KEY = 'discover.recommended_ids';

    public const OVERVIEW_KEY = 'catalog.presentation.overview.v1';

    public const SITEMAP_KEY = 'catalog.sitemap.xml.v1';

    public function saved(Model $model): void
    {
        self::flush();
    }

    public function deleted(Model $model): void
    {
        self::flush();
    }

    public static function flush(): void
    {
        Cache::forget(self::FILTER_OPTIONS_KEY);
        Cache::forget(self::RECOMMENDED_KEY);
        Cache::forget(self::OVERVIEW_KEY);
        Cache::forget(self::SITEMAP_KEY);
    }

    /**
     * Filter option lists stored as plain primitive arrays so cache drivers
     * remain compatible with `cache.serializable_classes => false`.
     *
     * @return array{
     *     locations: array<int, array{country: string, count: int}>,
     *     categories: array<int, array{industry_category: string, count: int}>,
     *     certifications: array<int, array{id: int, name: string, category: ?string, suppliers_count: int}>,
     *     hubCategories: array<int, array{name: string, slug: string, count: int}>
     * }
     */
    public static function filterOptions(): array
    {
        $options = Cache::remember(self::FILTER_OPTIONS_KEY, 600, function (): array {
            $categoryRows = Supplier::select('industry_category')
                ->selectRaw('count(*) as count')
                ->whereNotNull('industry_category')
                ->where('industry_category', '!=', '')
                ->groupBy('industry_category')
                ->orderBy('industry_category')
                ->toBase()
                ->get();

            $categoryCounts = $categoryRows->pluck('count', 'industry_category');

            $hubCategories = IndustryCategory::orderBy('name')
                ->get(['name', 'slug'])
                ->map(fn (IndustryCategory $cat): array => [
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'count' => (int) ($categoryCounts[$cat->name] ?? 0),
                ])
                ->all();

            if (empty($hubCategories)) {
                $hubCategories = $categoryRows
                    ->map(fn (object $row): array => [
                        'name' => (string) $row->industry_category,
                        'slug' => Str::slug((string) $row->industry_category),
                        'count' => (int) $row->count,
                    ])
                    ->all();
            }

            return [
                'locations' => Supplier::select('country')
                    ->selectRaw('count(*) as count')
                    ->whereNotNull('country')
                    ->where('country', '!=', '')
                    ->groupBy('country')
                    ->orderBy('country')
                    ->toBase()
                    ->get()
                    ->map(fn (object $row): array => [
                        'country' => (string) $row->country,
                        'count' => (int) $row->count,
                    ])
                    ->all(),
                'categories' => $categoryRows
                    ->map(fn (object $row): array => [
                        'industry_category' => (string) $row->industry_category,
                        'count' => (int) $row->count,
                    ])
                    ->all(),
                'certifications' => Certification::withCount('suppliers')
                    ->orderBy('name')
                    ->get()
                    ->map(fn (Certification $cert): array => [
                        'id' => (int) $cert->id,
                        'name' => (string) $cert->name,
                        'category' => $cert->category !== null ? (string) $cert->category : null,
                        'suppliers_count' => (int) $cert->suppliers_count,
                    ])
                    ->all(),
                'hubCategories' => $hubCategories,
            ];
        });

        return is_array($options) ? $options : [
            'locations' => [],
            'categories' => [],
            'certifications' => [],
            'hubCategories' => [],
        ];
    }
}
