<?php

namespace App\Livewire;

use App\Models\Supplier;
use App\Observers\CatalogCacheObserver;
use App\Support\CatalogPresentation;
use App\Support\SqlLike;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DiscoverSearch extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $query = '';

    #[Url(as: 'location')]
    public array|string $locations = [];

    #[Url(as: 'category')]
    public array|string $categories = [];

    #[Url(as: 'certification')]
    public array|string $certifications = [];

    #[Url]
    public string $sort = 'relevance';

    public array $compareIds = [];

    public function mount(): void
    {
        $this->normalizeFilters();
    }

    public function booted(): void
    {
        $this->normalizeFilters();
    }

    private function normalizeFilters(): void
    {
        if (is_string($this->categories)) {
            $this->categories = $this->categories !== '' ? [$this->categories] : [];
        }

        if (is_string($this->locations)) {
            $this->locations = $this->locations !== '' ? [$this->locations] : [];
        }

        if (is_string($this->certifications)) {
            $this->certifications = $this->certifications !== '' ? [$this->certifications] : [];
        }
    }

    public function updatingQuery(): void
    {
        $this->resetPage();
    }

    public function updatingLocations(): void
    {
        $this->resetPage();
    }

    public function updatingCategories(): void
    {
        $this->resetPage();
    }

    public function updatingCertifications(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['locations', 'categories', 'certifications', 'query']);
        $this->resetPage();
    }

    public function applyFilters(): void
    {
        $this->validate([
            'sort' => ['in:relevance,newest,company_size'],
            'categories' => ['array'],
            'locations' => ['array'],
            'certifications' => ['array'],
            'categories.*' => ['string', 'max:255'],
            'locations.*' => ['string', 'max:255'],
            'certifications.*' => ['string', 'max:255'],
        ]);
        $this->resetPage();
    }

    public function removeFilter(string $filter, string $value): void
    {
        if (! in_array($filter, ['locations', 'categories', 'certifications'], true)) {
            return;
        }

        $this->{$filter} = array_values(array_diff((array) $this->{$filter}, [$value]));
        $this->resetPage();
    }

    public function selectCategory(string $category): void
    {
        $this->categories = [$category];
        $this->resetPage();
    }

    public function selectKeyword(string $keyword): void
    {
        $this->query = $keyword;
        $this->resetPage();
    }

    public function toggleCompare(int $supplierId): void
    {
        if (in_array($supplierId, $this->compareIds)) {
            $this->compareIds = array_values(array_diff($this->compareIds, [$supplierId]));
        } else {
            if (count($this->compareIds) < 4) {
                $this->compareIds[] = $supplierId;
            }
        }
    }

    public function clearCompare(): void
    {
        $this->compareIds = [];
    }

    public function goToCompare()
    {
        if (empty($this->compareIds)) {
            return redirect()->route('compare.index');
        }

        return redirect()->route('compare.index', ['suppliers' => $this->compareIds]);
    }

    #[Computed]
    public function isHubMode(): bool
    {
        return empty($this->query) && empty($this->locations) && empty($this->categories) && empty($this->certifications);
    }

    #[Computed]
    public function recommendedSuppliers()
    {
        $ids = Cache::remember(CatalogCacheObserver::RECOMMENDED_KEY, 600, fn () => Supplier::query()
            ->orderByDesc('is_verified')
            ->limit(3)
            ->pluck('id')
            ->all());

        return Supplier::withCardStats()
            ->with(['tags', 'certifications.certification'])
            ->whereIn('id', is_array($ids) ? $ids : [])
            ->orderByDesc('is_verified')
            ->get();
    }

    #[Computed]
    public function selectedCompareSuppliers()
    {
        return Supplier::whereIn('id', $this->compareIds)->get(['id', 'name']);
    }

    /**
     * Filter option lists change rarely; stored as plain primitive arrays so cache drivers
     * remain compatible with `cache.serializable_classes => false`.
     *
     * @return array{
     *     locations: array<int, array{country: string, count: int}>,
     *     categories: array<int, array{industry_category: string, count: int}>,
     *     certifications: array<int, array{name: string, suppliers_count: int}>,
     *     hubCategories: array<int, array{name: string, count: int}>
     * }
     */
    private function filterOptions(): array
    {
        return CatalogCacheObserver::filterOptions();
    }

    public function render()
    {
        $suppliersQuery = Supplier::query()
            ->withCardStats()
            ->with(['tags', 'certifications.certification'])
            ->when(SqlLike::pattern($this->query) !== null, function ($q) {
                $pattern = SqlLike::pattern($this->query);
                $q->where(function ($sub) use ($pattern) {
                    $sub->where('name', 'like', $pattern)
                        ->orWhere('industry_category', 'like', $pattern)
                        ->orWhere('country', 'like', $pattern)
                        ->orWhere('city', 'like', $pattern)
                        ->orWhere('description', 'like', $pattern)
                        ->orWhereHas('tags', fn ($tagQuery) => $tagQuery->where('tag_name', 'like', $pattern));
                });
            })
            ->when(! empty($this->locations), fn ($q) => $q->where(fn ($locationQuery) => $locationQuery
                ->whereIn('country', (array) $this->locations)
                ->orWhereIn('city', (array) $this->locations)))
            ->when(! empty($this->categories), fn ($q) => $q->whereIn('industry_category', (array) $this->categories))
            ->when(! empty($this->certifications), function ($q) {
                $q->whereHas('certifications.certification', fn ($sub) => $sub->whereIn('name', (array) $this->certifications));
            });

        match ($this->sort) {
            'newest' => $suppliersQuery->latest()->orderByDesc('id'),
            'company_size' => $suppliersQuery->orderByDesc('company_size')->orderBy('id'),
            default => $suppliersQuery->orderByDesc('is_verified')->orderBy('name'),
        };

        $options = $this->filterOptions();

        return view('livewire.discover-search', [
            'suppliers' => $suppliersQuery->paginate(12),
            'locationOptions' => $options['locations'] ?? [],
            'categoryOptions' => $options['categories'] ?? [],
            'certificationOptions' => $options['certifications'] ?? [],
            'hubCategories' => CatalogPresentation::categories(),
            'cityOptions' => Supplier::query()->whereNotNull('city')->where('city', '!=', '')->distinct()->orderBy('city')->pluck('city'),
        ]);
    }
}
