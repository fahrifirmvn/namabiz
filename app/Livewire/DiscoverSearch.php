<?php

namespace App\Livewire;

use App\Models\Certification;
use App\Models\Supplier;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DiscoverSearch extends Component
{
    use WithPagination;

    #[Url]
    public string $query = '';

    public array $locations = [];

    public array $categories = [];

    public array $certifications = [];

    public string $sort = 'relevance';

    protected $queryString = [];

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

    public function clearFilters(): void
    {
        $this->reset(['locations', 'categories', 'certifications']);
    }

    public function render()
    {
        $suppliers = Supplier::query()
            ->with(['tags', 'documents'])
            ->when($this->query !== '', function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->query}%")
                        ->orWhere('industry_category', 'like', "%{$this->query}%")
                        ->orWhere('country', 'like', "%{$this->query}%");
                });
            })
            ->when($this->locations, fn ($q) => $q->whereIn('country', $this->locations))
            ->when($this->categories, fn ($q) => $q->whereIn('industry_category', $this->categories))
            ->when($this->certifications, function ($q) {
                $q->whereHas('certifications.certification', fn ($q) => $q->whereIn('name', $this->certifications));
            })
            ->when($this->sort === 'newest', fn ($q) => $q->latest())
            ->when($this->sort === 'company_size', fn ($q) => $q->orderByDesc('company_size'))
            ->when($this->sort === 'relevance', fn ($q) => $q->orderByDesc('is_verified'))
            ->paginate(12);

        $locationOptions = Supplier::select('country')->distinct()->orderBy('country')->pluck('country');
        $categoryOptions = Supplier::select('industry_category')->distinct()->orderBy('industry_category')->pluck('industry_category');
        $certificationOptions = Certification::withCount('suppliers')->get();

        return view('livewire.discover-search', [
            'suppliers' => $suppliers,
            'locationOptions' => $locationOptions,
            'categoryOptions' => $categoryOptions,
            'certificationOptions' => $certificationOptions,
        ]);
    }
}
