<?php

namespace App\Livewire;

use App\Models\Supplier;
use App\Support\PremiumAccess;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ComparisonTable extends Component
{
    public const MAX_SUPPLIERS = 3;

    public array $supplierIds = [];

    public string $search = '';

    public function mount(): void
    {
        $this->supplierIds = Supplier::query()->inRandomOrder()->limit(2)->pluck('id')->toArray();
    }

    /**
     * Re-enforce the max-supplier cap on every request, since $supplierIds is a
     * public property and could otherwise be set directly to an arbitrary array
     * via a crafted Livewire "updates" payload, bypassing addSupplier()'s check.
     */
    public function updated($name): void
    {
        if ($name === 'supplierIds') {
            $this->supplierIds = array_slice(array_unique($this->supplierIds), 0, self::MAX_SUPPLIERS);
        }
    }

    public function hydrate(): void
    {
        $this->supplierIds = array_slice(array_unique($this->supplierIds), 0, self::MAX_SUPPLIERS);
    }

    public function addSupplier(int $supplierId): void
    {
        if (count($this->supplierIds) >= self::MAX_SUPPLIERS || in_array($supplierId, $this->supplierIds)) {
            return;
        }

        $this->supplierIds[] = $supplierId;
        $this->search = '';
    }

    public function removeSupplier(int $supplierId): void
    {
        $this->supplierIds = array_values(array_diff($this->supplierIds, [$supplierId]));
    }

    #[Computed]
    public function suppliers()
    {
        return Supplier::with(['certifications.certification', 'documents'])
            ->whereIn('id', $this->supplierIds)
            ->get()
            ->sortBy(fn ($supplier) => array_search($supplier->id, $this->supplierIds))
            ->values();
    }

    #[Computed]
    public function searchResults()
    {
        if ($this->search === '') {
            return collect();
        }

        return Supplier::where('name', 'like', "%{$this->search}%")
            ->whereNotIn('id', $this->supplierIds)
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function isPremium(): bool
    {
        return PremiumAccess::isPremium(auth()->user());
    }

    public function certificationFor(Supplier $supplier, string $name): ?object
    {
        return $supplier->certifications->first(fn ($c) => $c->certification?->name === $name);
    }

    public function documentFor(Supplier $supplier, string $title): ?object
    {
        return $supplier->documents->first(fn ($d) => $d->title === $title);
    }

    public function render()
    {
        return view('livewire.comparison-table');
    }
}
