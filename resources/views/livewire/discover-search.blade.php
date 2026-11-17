<div class="nb-container py-8 pb-32" x-data="{ panel: null, compact: false }" @keydown.escape.window="panel = null"
     x-effect="$dispatch('compare-updated', { count: $wire.compareIds.length })">
    @php($activeFilterCount = count($locations) + count($categories) + count($certifications))
    <header class="nb-discover-header mb-6">
        <p class="nb-eyebrow">JELAJAHI NAMABIZ</p>
        <h1 class="mt-2 text-[28px] leading-[34px] font-bold tracking-tight md:text-4xl">Cari pemasok</h1>
        <p class="mt-3 text-sm leading-[22px] text-text-muted">Dari bahan baku hingga kemasan. Temukan calon mitra bisnis Anda.</p>
    </header>

    <div class="nb-catalog-toolbar sticky z-30 -mx-4 bg-surface/95 px-4 pt-2 pb-3 backdrop-blur-md" @scroll.window.passive="compact = window.scrollY > 140">
        <form wire:submit="applyFilters" class="nb-search transition-all duration-180" :class="compact ? '!h-12' : ''" role="search">
            <x-heroicon-o-magnifying-glass class="ml-2 h-5 w-5 shrink-0 text-text-muted" />
            <input id="catalog-search" type="search" wire:model.live.debounce.300ms="query" aria-label="Cari nama pemasok, produk, atau bahan baku" placeholder="Cari pemasok atau bahan baku">
            <button type="submit" class="nb-icon-button !bg-slate-900 !text-white" aria-label="Cari"><x-heroicon-o-arrow-right class="h-5 w-5" /></button>
        </form>
        <div class="mt-3 grid grid-cols-2 gap-3">
            <button type="button" class="nb-secondary" @click="panel = 'filters'" :aria-expanded="panel === 'filters'" aria-controls="catalog-sheet"><x-heroicon-o-adjustments-horizontal class="h-5 w-5" />Filter @if($activeFilterCount)({{ $activeFilterCount }})@endif</button>
            <button type="button" class="nb-secondary" @click="panel = 'sort'" :aria-expanded="panel === 'sort'" aria-controls="catalog-sheet"><x-heroicon-o-arrows-up-down class="h-5 w-5" />Urutkan</button>
        </div>
    </div>

    @if($activeFilterCount || $query !== '')
        <div class="nb-scroll-row mt-3" aria-label="Filter aktif">
            @if($query !== '')<button type="button" class="nb-secondary !min-h-[44px] shrink-0 !px-3" wire:click="$set('query', '')" aria-label="Hapus kata kunci {{ $query }}">{{ $query }}<x-heroicon-o-x-mark class="h-4 w-4" /></button>@endif
            @foreach(['locations' => $locations, 'categories' => $categories, 'certifications' => $certifications] as $filter => $values)
                @foreach($values as $value)
                    <button type="button" wire:click="removeFilter(@js($filter), @js($value))" class="nb-secondary !min-h-[44px] shrink-0 !px-3" aria-label="Hapus filter {{ \App\Support\SupplierFormat::category($value) }}">{{ \App\Support\SupplierFormat::category($value) }}<x-heroicon-o-x-mark class="h-4 w-4" /></button>
                @endforeach
            @endforeach
            <button type="button" wire:click="clearFilters" class="nb-ghost shrink-0">Reset semua</button>
        </div>
    @else
        <div class="nb-scroll-row mt-3 items-center">
            <span class="inline-flex shrink-0 items-center gap-1.5 text-13 text-text-muted"><x-heroicon-o-arrow-trending-up class="h-4 w-4" />Populer:</span>
            @foreach(['Kemasan', 'Resin', 'Mesin pengemas'] as $keyword)<button type="button" class="nb-chip min-h-[44px] hover:bg-brand-50" wire:click="selectKeyword(@js($keyword))">{{ $keyword }}</button>@endforeach
        </div>
    @endif

    <section class="mt-6" aria-labelledby="catalog-categories">
        <h2 id="catalog-categories" class="text-lg font-semibold mb-4">Kategori industri</h2>
        <div class="nb-scroll-row gap-3 md:grid md:grid-cols-3 lg:grid-cols-5">
            <button type="button" wire:click="$set('categories', [])" class="nb-category-tile nb-card p-4 text-left min-w-[140px] shrink-0 snap-start {{ empty($categories) ? '!bg-brand-50 !border-brand-200' : '' }}">
                <x-heroicon-o-squares-2x2 class="h-5 w-5 mb-3 text-brand-700" /><span class="block text-sm font-semibold">Semua kategori</span><span class="mt-2 block text-13 text-text-muted">Telusuri katalog</span>
            </button>
            @foreach($hubCategories as $category)
                <button type="button" wire:key="category-{{ md5($category->name) }}" wire:click="selectCategory(@js($category->name))" class="nb-category-tile nb-card p-4 text-left w-[160px] md:w-auto shrink-0 snap-start {{ in_array($category->name, $categories) ? '!bg-brand-50 !border-brand-200' : '' }}">
                    <x-dynamic-component :component="'heroicon-o-'.\App\Support\SupplierFormat::categoryIcon($category->name)" class="h-5 w-5 mb-3 text-brand-700" /><span class="block text-sm font-semibold leading-5">{{ \App\Support\SupplierFormat::category($category->name) }}</span><span class="mt-2 block text-13 text-text-muted">{{ $category->count }} pemasok</span>
                </button>
            @endforeach
        </div>
    </section>

    <section class="mt-8" aria-labelledby="supplier-results">
        <div class="flex flex-wrap items-end justify-between gap-2 mb-5">
            <div><h2 id="supplier-results" class="text-xl font-semibold">{{ $this->isHubMode ? 'Pemasok pilihan' : 'Hasil pencarian' }}</h2><p class="mt-1 text-sm text-text-muted" role="status">{{ \App\Support\SupplierFormat::number($suppliers->total()) }} pemasok{{ $this->isHubMode ? ' dalam katalog' : ' ditemukan' }}</p></div>
            <span class="text-13 text-text-muted">{{ ['relevance' => 'Paling relevan', 'newest' => 'Paling baru', 'company_size' => 'Ukuran perusahaan'][$sort] ?? 'Paling relevan' }}</span>
        </div>
        <p wire:offline class="mb-4 rounded-xl bg-amber-50 p-4 text-sm text-amber-700" role="alert">Koneksi terputus. Periksa jaringan Anda, lalu coba lagi.</p>
        <div x-data="{ visible: false }" @catalog-request-failed.window="visible = true" x-show="visible" x-cloak class="mb-4 rounded-xl bg-red-50 p-4 text-sm text-red-700" role="alert"><p>Pencarian gagal dimuat. Silakan coba lagi.</p><button type="button" wire:click="$refresh" @click="visible = false" class="nb-secondary mt-3">Coba lagi</button></div>
        <div wire:loading.grid wire:target="query,applyFilters,clearFilters,selectCategory,selectKeyword,removeFilter,setPage,nextPage,previousPage,categories" class="grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" role="status" aria-label="Memuat pemasok">
            @for($i = 0; $i < 3; $i++)<x-supplier-card-skeleton />@endfor
        </div>
        <div wire:loading.remove wire:target="query,applyFilters,clearFilters,selectCategory,selectKeyword,removeFilter,setPage,nextPage,previousPage,categories" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-stretch">
            @forelse($suppliers as $supplier)
                <x-supplier-card wire:key="supplier-{{ $supplier->id }}" :supplier="$supplier" :selectable="true" :selected="in_array($supplier->id, $compareIds)" :limit-reached="count($compareIds) >= 4" />
            @empty
                <div class="nb-card col-span-full p-6 py-12 text-center">
                    <x-heroicon-o-magnifying-glass class="h-8 w-8 text-text-muted mx-auto mb-4" /><h3 class="text-lg font-semibold">Tidak ada pemasok untuk filter ini</h3>
                    <p class="mt-3 text-sm text-text-muted">Coba kata kunci lain atau kurangi filter pencarian Anda.</p>
                    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:justify-center"><button type="button" wire:click="clearFilters" class="nb-primary">Reset filter</button><a href="{{ \App\Support\PortalContent::pageUrl('help') }}" class="nb-secondary">Buka panduan bantuan</a></div>
                </div>
            @endforelse
        </div>
        <div class="mt-8">{{ $suppliers->links('components.ui.catalog-pagination') }}</div>
    </section>

    <aside class="nb-card mt-12 p-5 md:p-8 grid gap-5 lg:grid-cols-[1fr_auto] lg:items-center">
        <div class="flex items-start gap-3"><x-heroicon-o-chat-bubble-left-right class="h-6 w-6 shrink-0 text-brand-700" /><div><h2 class="text-base font-semibold">Belum menemukan spesifikasi yang tepat?</h2><p class="mt-2 text-sm text-text-muted">Gunakan panduan penelusuran atau jelajahi kategori lainnya.</p></div></div>
        <div class="grid gap-3">
            @if($curatorUrl = \App\Support\PortalContent::whatsappUrl())<a href="{{ $curatorUrl }}" class="nb-accent" target="_blank" rel="noopener noreferrer">Hubungi kurator via WhatsApp</a>@else<a href="{{ \App\Support\PortalContent::pageUrl('help') }}" class="nb-accent">Buka panduan bantuan</a>@endif
            <button type="button" class="nb-secondary" @click="document.getElementById('catalog-categories').scrollIntoView({ block: 'start' })">Telusuri kategori</button>
        </div>
    </aside>

    <template x-teleport="body">
        <div x-show="panel !== null" x-cloak x-transition.opacity.duration.300ms class="fixed inset-0 z-[70] flex items-end md:items-center md:justify-center bg-slate-900/40 backdrop-blur-sm"
             @click.self="panel = null" x-trap.inert.noscroll="panel !== null" role="dialog" aria-modal="true" :aria-label="panel === 'sort' ? 'Urutkan pemasok' : 'Filter pemasok'">
            <div id="catalog-sheet" x-show="panel !== null" x-transition:enter="nb-panel-transition" x-transition:enter-start="nb-sheet-from" x-transition:enter-end="nb-panel-to"
                 x-transition:leave="nb-panel-transition" x-transition:leave-start="nb-panel-to" x-transition:leave-end="nb-sheet-from"
                 class="w-full max-h-[85dvh] md:max-w-xl flex flex-col rounded-t-[20px] md:rounded-[20px] bg-white shadow-tray">
                <div class="mx-auto mt-3 h-1 w-10 rounded-full bg-slate-300 md:hidden" aria-hidden="true"></div>
                <div class="flex items-center justify-between gap-3 p-5 border-b border-border"><h2 class="text-lg font-semibold" x-text="panel === 'sort' ? 'Urutkan pemasok' : 'Filter pemasok'"></h2><button type="button" @click="panel = null" class="nb-icon-button" aria-label="Tutup filter"><x-heroicon-o-x-mark class="h-5 w-5" /></button></div>
                <div class="overflow-y-auto overscroll-contain p-5 space-y-6">
                    <div x-show="panel === 'sort'">
                        @foreach(['relevance' => 'Paling relevan', 'newest' => 'Paling baru', 'company_size' => 'Ukuran perusahaan'] as $value => $label)
                            <label class="flex min-h-[48px] items-center gap-3 text-sm"><input type="radio" wire:model="sort" value="{{ $value }}" name="sort" class="text-brand-700 focus:ring-brand-600">{{ $label }}</label>
                        @endforeach
                    </div>
                    <div x-show="panel === 'filters'" class="space-y-6">
                        <fieldset><legend class="text-sm font-semibold mb-2">Kategori industri</legend>
                            @foreach($categoryOptions as $category)<label class="flex min-h-[44px] items-center gap-3 text-sm"><input type="checkbox" wire:model="categories" value="{{ $category['industry_category'] }}" class="rounded text-brand-700 border-border">{{ \App\Support\SupplierFormat::category($category['industry_category']) }}<span class="ml-auto text-text-muted text-xs">{{ $category['count'] }}</span></label>@endforeach
                        </fieldset>
                        <fieldset><legend class="text-sm font-semibold mb-2">Lokasi</legend>
                            @foreach($locationOptions as $location)<label class="flex min-h-[44px] items-center gap-3 text-sm"><input type="checkbox" wire:model="locations" value="{{ $location['country'] }}" class="rounded text-brand-700 border-border">{{ $location['country'] }}</label>@endforeach
                            @foreach($cityOptions as $city)<label class="flex min-h-[44px] items-center gap-3 text-sm"><input type="checkbox" wire:model="locations" value="{{ $city }}" class="rounded text-brand-700 border-border">{{ $city }}</label>@endforeach
                        </fieldset>
                        <fieldset><legend class="text-sm font-semibold mb-2">Sertifikasi</legend>
                            @forelse($certificationOptions as $certification)<label class="flex min-h-[44px] items-center gap-3 text-sm"><input type="checkbox" wire:model="certifications" value="{{ $certification['name'] }}" class="rounded text-brand-700 border-border">{{ $certification['name'] }}</label>@empty<p class="text-sm text-text-muted">Belum ada sertifikasi dalam katalog.</p>@endforelse
                        </fieldset>
                        <div class="rounded-xl bg-surface p-4 text-sm text-text-muted"><p>Filter kapasitas, MOQ, lead time, dan waktu respons belum tersedia.</p><a href="{{ \App\Support\PortalContent::pageUrl('help') }}" class="mt-2 inline-flex min-h-[44px] items-center text-brand-700 underline underline-offset-4">Pelajari satuan dan status data</a></div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 p-5 border-t border-border" style="padding-bottom: max(20px, env(safe-area-inset-bottom))">
                    <button type="button" wire:click="clearFilters" class="nb-secondary">Reset</button>
                    <button type="button" wire:click="applyFilters" @click="panel = null" class="nb-primary" wire:loading.attr="disabled" wire:target="applyFilters">Terapkan</button>
                </div>
            </div>
        </div>
    </template>
    <x-ui.compare-tray :selected-suppliers="$this->selectedCompareSuppliers" :max-suppliers="4" />
</div>
