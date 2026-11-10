<?php

namespace App\Livewire;

use App\Models\Certification;
use App\Models\Supplier;
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
        return Supplier::with(['tags', 'certifications.certification', 'documents', 'reviews'])
            ->orderByDesc('is_verified')
            ->limit(3)
            ->get();
    }

    #[Computed]
    public function selectedCompareSuppliers()
    {
        return Supplier::whereIn('id', $this->compareIds)->get(['id', 'name']);
    }

    public function render()
    {
        $suppliersQuery = Supplier::query()
            ->with(['tags', 'certifications.certification', 'documents', 'reviews'])
            ->when($this->query !== '', function ($q) {
                $keyword = trim($this->query);
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('name', 'like', "%{$keyword}%")
                        ->orWhere('industry_category', 'like', "%{$keyword}%")
                        ->orWhere('country', 'like', "%{$keyword}%")
                        ->orWhere('city', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%");
                });
            })
            ->when(! empty($this->locations), fn ($q) => $q->whereIn('country', (array) $this->locations))
            ->when(! empty($this->categories), fn ($q) => $q->whereIn('industry_category', (array) $this->categories))
            ->when(! empty($this->certifications), function ($q) {
                $q->whereHas('certifications.certification', fn ($sub) => $sub->whereIn('name', (array) $this->certifications));
            });

        match ($this->sort) {
            'newest' => $suppliersQuery->latest(),
            'company_size' => $suppliersQuery->orderByDesc('company_size'),
            default => $suppliersQuery->orderByDesc('is_verified')->orderBy('name'),
        };

        $suppliers = $suppliersQuery->paginate(12);

        $locationOptions = Supplier::select('country')
            ->selectRaw('count(*) as count')
            ->groupBy('country')
            ->orderBy('country')
            ->get();

        $categoryOptions = Supplier::select('industry_category')
            ->selectRaw('count(*) as count')
            ->groupBy('industry_category')
            ->orderBy('industry_category')
            ->get();

        $certificationOptions = Certification::withCount('suppliers')->get();

        return view('livewire.discover-search', [
            'suppliers' => $suppliers,
            'locationOptions' => $locationOptions,
            'categoryOptions' => $categoryOptions,
            'certificationOptions' => $certificationOptions,
        ]);
    }
}
