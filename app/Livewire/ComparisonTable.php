<?php

namespace App\Livewire;

use App\Models\Supplier;
use App\Support\PremiumAccess;
use App\Support\SqlLike;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ComparisonTable extends Component
{
    public const MAX_SUPPLIERS = 4;

    public array $supplierIds = [];

    public string $search = '';

    public bool $onlyDifferences = false;

    public function mount(): void
    {
        $requested = request('suppliers', request('ids'));
        if (is_array($requested) && ! empty($requested)) {
            $this->supplierIds = array_slice(array_map('intval', $requested), 0, self::MAX_SUPPLIERS);
        } elseif (is_string($requested) && ! empty($requested)) {
            $this->supplierIds = array_slice(array_map('intval', explode(',', $requested)), 0, self::MAX_SUPPLIERS);
        } else {
            $this->supplierIds = Supplier::query()->orderByDesc('is_verified')->limit(3)->pluck('id')->toArray();
        }
    }

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

    public function toggleDifferences(): void
    {
        $this->onlyDifferences = ! $this->onlyDifferences;
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
        return Supplier::with(['certifications.certification', 'documents', 'tags'])
            ->withCardStats()
            ->whereIn('id', $this->supplierIds)
            ->get()
            ->sortBy(fn ($supplier) => array_search($supplier->id, $this->supplierIds))
            ->values();
    }

    #[Computed]
    public function searchResults()
    {
        $pattern = SqlLike::pattern($this->search);
        if ($pattern === null) {
            return collect();
        }

        return Supplier::where('name', 'like', $pattern)
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
        $needle = mb_strtolower($name);

        return $supplier->certifications->first(
            fn ($c): bool => $c->certification !== null
                && str_starts_with(mb_strtolower($c->certification->name), $needle)
        );
    }

    /**
     * Determine whether a row should be shown when "Tampilkan perbedaan saja" is enabled.
     */
    public function shouldShowRow(callable $extractor): bool
    {
        if (! $this->onlyDifferences || $this->suppliers->count() <= 1) {
            return true;
        }

        return $this->suppliers->map($extractor)->unique()->count() > 1;
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
