<?php

namespace App\Support;

use App\Models\Supplier;
use App\Observers\CatalogCacheObserver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CatalogPresentation
{
    /** @return Collection<int, object{ name: string, count: int }> */
    public static function categories(): Collection
    {
        return Supplier::query()
            ->selectRaw('industry_category as name, count(*) as count')
            ->whereNotNull('industry_category')
            ->where('industry_category', '!=', '')
            ->groupBy('industry_category')
            ->orderByDesc('count')
            ->orderBy('industry_category')
            ->toBase()
            ->get();
    }

    /** @return array<string, mixed> */
    public static function overview(): array
    {
        $cached = Cache::remember(CatalogCacheObserver::OVERVIEW_KEY, 600, function (): array {
            $categories = self::categories()
                ->map(fn (object $row): array => [
                    'name' => (string) $row->name,
                    'count' => (int) $row->count,
                ])
                ->all();

            $previews = Supplier::query()
                ->withCount('documents')
                ->orderByDesc('documents_count')
                ->orderByDesc('is_verified')
                ->orderBy('name')
                ->limit(3)
                ->get(['id', 'moq']);

            $moqs = $previews->map(fn (Supplier $supplier): ?float => SupplierFormat::quantity($supplier->moq));

            return [
                'categories' => $categories,
                'stats' => [
                    'suppliers' => Supplier::count(),
                    'verified' => Supplier::where('is_verified', true)->count(),
                    'categories' => count($categories),
                ],
                'preview_ids' => $previews->pluck('id')->all(),
                'lowest_moq' => $moqs->count() >= 2 && $moqs->every(fn (?float $moq): bool => $moq !== null && $moq > 0)
                    ? $moqs->min()
                    : null,
            ];
        });

        $previewIds = array_values(array_map('intval', (array) ($cached['preview_ids'] ?? [])));
        $previews = $previewIds !== []
            ? Supplier::query()
                ->withCount('documents')
                ->with(['certifications.certification'])
                ->whereIn('id', $previewIds)
                ->get()
                ->sortBy(fn (Supplier $supplier): int => (int) array_search($supplier->id, $previewIds, true))
                ->values()
            : collect();

        $catalogCategories = collect((array) ($cached['categories'] ?? []))
            ->map(fn (array $row): object => (object) [
                'name' => (string) $row['name'],
                'count' => (int) $row['count'],
            ]);

        return [
            'catalogCategories' => $catalogCategories,
            'catalogStats' => (array) ($cached['stats'] ?? ['suppliers' => 0, 'verified' => 0, 'categories' => 0]),
            'previewSuppliers' => $previews,
            'lowestPreviewMoq' => $cached['lowest_moq'] ?? null,
        ];
    }
}
