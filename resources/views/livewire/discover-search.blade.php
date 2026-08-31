<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <aside class="lg:w-1/4 shrink-0">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-text-primary">Filters</h2>
                <button wire:click="clearFilters" class="text-sm text-text-secondary underline">Clear All</button>
            </div>

            <div class="border-t border-gray-200 pt-4 mb-4">
                <h3 class="font-semibold text-sm mb-3">Location</h3>
                <div class="space-y-2">
                    @foreach($locationOptions as $location)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" wire:model.live="locations" value="{{ $location }}" class="rounded border-gray-300">
                            {{ $location }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4 mb-4">
                <h3 class="font-semibold text-sm mb-3">Category</h3>
                <div class="space-y-2">
                    @foreach($categoryOptions as $category)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" wire:model.live="categories" value="{{ $category }}" class="rounded border-gray-300">
                            {{ $category }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h3 class="font-semibold text-sm mb-3">Certifications</h3>
                <div class="space-y-2">
                    @foreach($certificationOptions as $certification)
                        <label class="flex items-center justify-between gap-2 text-sm text-gray-700">
                            <span class="flex items-center gap-2">
                                <input type="checkbox" wire:model.live="certifications" value="{{ $certification->name }}" class="rounded border-gray-300">
                                {{ $certification->name }}
                            </span>
                            <span class="text-gray-400">{{ $certification->suppliers_count }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </aside>

        <div class="lg:w-3/4">
            <div class="flex items-start justify-between mb-6 gap-4 flex-wrap">
                <div>
                    <h1 class="text-3xl font-bold text-text-primary">
                        @if($query)
                            Results for "{{ $query }}"
                        @else
                            Discover Suppliers
                        @endif
                    </h1>
                    <p class="text-text-secondary mt-1">Showing {{ $suppliers->firstItem() ?? 0 }}-{{ $suppliers->lastItem() ?? 0 }} of {{ $suppliers->total() }} suppliers</p>
                </div>

                <div class="flex items-center gap-2">
                    <label for="supplier-sort" class="text-sm text-text-secondary">Sort by:</label>
                    <select id="supplier-sort" wire:model.live="sort" class="rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-black">
                        <option value="relevance">Relevance</option>
                        <option value="newest">Newest</option>
                        <option value="company_size">Company Size</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($suppliers as $supplier)
                    <x-supplier-card :supplier="$supplier" />
                @empty
                    <p class="text-text-secondary md:col-span-2" role="status">No suppliers match your filters.</p>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>
</div>
