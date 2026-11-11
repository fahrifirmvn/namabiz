<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-24">
    @if($this->isHubMode)
        <!-- ================= MODE HUB (JELAJAHI) ================= -->
        <div class="space-y-12">
            <!-- Header Hub -->
            <div>
                <span class="text-xs font-semibold text-text-muted uppercase tracking-widest">
                    KATALOG TERPADU / DIREKTORI B2B INDONESIA
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-text-primary tracking-tight mt-1">
                    Cari pemasok
                </h1>
                <p class="text-sm sm:text-base text-text-secondary mt-1.5">
                    Telusuri berdasarkan kategori industri atau mulai dari kata kunci bahan baku Anda.
                </p>
            </div>

            <!-- Search Bar in Hub -->
            <div class="max-w-3xl">
                <div class="relative flex items-center shadow-xs rounded-2xl bg-white border border-border p-2 focus-within:ring-2 focus-within:ring-brand-600 focus-within:border-brand-600 transition">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-text-muted ml-3 shrink-0" />
                    <input type="text"
                           wire:model.live.debounce.300ms="query"
                           aria-label="Cari pemasok atau bahan baku"
                           placeholder="Cari pemasok, spesifikasi, atau bahan baku..."
                           class="w-full pl-3 pr-4 py-2 text-sm text-text-primary placeholder-text-muted bg-transparent border-none focus:outline-none focus:ring-0">
                    <div class="mr-2 hidden sm:block">
                        <kbd class="px-2 py-1 text-xs text-text-muted bg-surface border border-border rounded font-mono">/</kbd>
                    </div>
                </div>

                <!-- Recent Searches -->
                <div class="mt-3.5 flex flex-wrap items-center justify-between gap-2 text-13">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-text-secondary font-medium">Pencarian populer:</span>
                        <button type="button" wire:click="selectKeyword('kardus bergelombang')" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white hover:bg-surface-subtle border border-border rounded-full text-text-secondary hover:text-text-primary transition nb-focus-ring">
                            <x-heroicon-o-clock class="w-3.5 h-3.5 text-text-muted" /> kardus bergelombang
                        </button>
                        <button type="button" wire:click="selectKeyword('resin PET')" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white hover:bg-surface-subtle border border-border rounded-full text-text-secondary hover:text-text-primary transition nb-focus-ring">
                            <x-heroicon-o-clock class="w-3.5 h-3.5 text-text-muted" /> resin PET
                        </button>
                        <button type="button" wire:click="selectKeyword('mesin pengemas')" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white hover:bg-surface-subtle border border-border rounded-full text-text-secondary hover:text-text-primary transition nb-focus-ring">
                            <x-heroicon-o-clock class="w-3.5 h-3.5 text-text-muted" /> mesin pengemas
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kategori Industri Grid -->
            <div>
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-text-primary">Kategori industri</h2>
                    <span class="text-13 font-medium text-text-secondary">{{ count($hubCategories) }} Sektor Terdaftar</span>
                </div>

                @php
                    $categoryIcons = [
                        'Packaging Manufacturer' => 'archive-box',
                        'Distributor & Converter' => 'truck',
                        'Contract Manufacturer' => 'cog-6-tooth',
                        'Raw Material Supplier' => 'beaker',
                        'Chemical & Ingredients' => 'funnel',
                        'Spices & Coconut' => 'sparkles',
                        'Logistics & Warehousing' => 'building-office',
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    @forelse($hubCategories as $cat)
                        <button type="button"
                                wire:click="selectCategory(@js($cat['name']))"
                                class="p-5 nb-card-interactive text-left group flex items-center justify-between gap-3 nb-focus-ring">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-surface-subtle border border-border/80 flex items-center justify-center text-text-primary group-hover:bg-brand-50 group-hover:text-brand-700 group-hover:border-brand-200 transition shrink-0">
                                    <x-dynamic-component :component="'heroicon-o-' . ($categoryIcons[$cat['name']] ?? 'building-office-2')" class="w-5 h-5" />
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-semibold text-text-primary text-sm truncate" title="{{ $cat['name'] }}">{{ $cat['name'] }}</h3>
                                    <p class="text-13 text-text-secondary mt-0.5">{{ $cat['count'] }} pemasok</p>
                                </div>
                            </div>
                            <x-heroicon-s-chevron-right class="w-4 h-4 text-text-muted group-hover:text-text-primary group-hover:translate-x-0.5 transition-transform shrink-0" />
                        </button>
                    @empty
                        <div class="col-span-full py-8 text-center text-sm text-text-secondary bg-white rounded-2xl border border-dashed border-border">
                            Belum ada kategori industri yang terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Rekomendasi "Untuk Anda" -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-text-primary">Untuk Anda</h2>
                        <p class="text-13 text-text-secondary mt-0.5">Pilihan pabrik berdasarkan kapasitas produksi & kelengkapan dokumen katalog.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6 items-stretch">
                    @foreach($this->recommendedSuppliers as $rec)
                        <x-supplier-card
                            :supplier="$rec"
                            :selectable="true"
                            :selected="in_array($rec->id, $compareIds)"
                            :limit-reached="count($compareIds) >= 4"
                        />
                    @endforeach
                </div>
            </div>

            <!-- Banner Bantuan & Konsultasi Langsung -->
            <div class="bg-surface rounded-2xl border border-border p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white border border-border flex items-center justify-center text-brand-600 shadow-2xs shrink-0">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-text-primary text-base">Tidak menemukan spesifikasi bahan baku yang Anda butuhkan?</h3>
                        <p class="text-13 sm:text-sm text-text-secondary mt-0.5">Konsultasikan kebutuhan pasokan industri Anda langsung dengan tim kurator Namabiz.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('discover.index', ['q' => 'Kemasan']) }}" class="h-10 px-4 text-13 font-semibold text-text-primary bg-white border border-border rounded-xl hover:bg-surface-subtle transition inline-flex items-center justify-center nb-focus-ring">
                        Telusuri Kategori
                    </a>
                    <a href="https://wa.me/6281234567890?text={{ urlencode('Halo Tim Namabiz, saya mencari rekomendasi pemasok untuk kebutuhan industri.') }}" target="_blank" rel="noopener noreferrer" class="h-10 px-4 text-13 font-semibold text-white bg-brand-600 rounded-xl hover:bg-brand-700 transition inline-flex items-center justify-center gap-1.5 nb-focus-ring">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4" />
                        <span>Hubungi Kurator WA</span>
                    </a>
                </div>
            </div>
        </div>

    @else
        <!-- ================= MODE HASIL PENCARIAN ================= -->
        <div>
            <!-- Breadcrumbs -->
            <nav class="text-13 text-text-secondary mb-5 flex items-center gap-2" aria-label="Breadcrumb">
                <a href="{{ route('discover.index') }}" wire:click.prevent="clearFilters" class="hover:text-text-primary transition nb-focus-ring rounded">Direktori</a>
                <span class="text-text-muted" aria-hidden="true">/</span>
                <span>Manufaktur &amp; Kemasan</span>
                <span class="text-text-muted" aria-hidden="true">/</span>
                <span class="text-text-primary font-semibold">Indonesia</span>
            </nav>

            <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
                <!-- Left Sidebar Filters -->
                <aside class="lg:w-72 shrink-0">
                    <div class="nb-card p-5 space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-border-subtle">
                            <h2 class="text-xs font-bold text-text-primary uppercase tracking-wider flex items-center gap-2">
                                <x-heroicon-o-adjustments-horizontal class="w-4 h-4 text-text-secondary" />
                                <span>Parameter Filter</span>
                            </h2>
                            <button type="button" wire:click="clearFilters" class="text-xs text-text-secondary hover:text-text-primary underline font-medium nb-focus-ring rounded">
                                Reset
                            </button>
                        </div>

                        <!-- Lokasi Filter -->
                        <div>
                            <h3 class="font-semibold text-xs uppercase text-text-secondary mb-3 tracking-wider">Lokasi Geografis</h3>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                @foreach($locationOptions as $loc)
                                    <label class="flex items-center justify-between text-13 text-text-secondary hover:text-text-primary cursor-pointer py-0.5">
                                        <span class="flex items-center gap-2.5 min-w-0">
                                            <input type="checkbox" wire:model.live="locations" value="{{ $loc['country'] }}" class="rounded border-gray-300 text-gray-900 focus:ring-brand-600">
                                            <span class="truncate">{{ $loc['country'] }}</span>
                                        </span>
                                        <span class="text-text-muted text-xs font-medium">{{ $loc['count'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Kategori Filter -->
                        <div class="pt-4 border-t border-border-subtle">
                            <h3 class="font-semibold text-xs uppercase text-text-secondary mb-3 tracking-wider">Kategori Industri</h3>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                @foreach($categoryOptions as $cat)
                                    <label class="flex items-center justify-between text-13 text-text-secondary hover:text-text-primary cursor-pointer py-0.5">
                                        <span class="flex items-center gap-2.5 min-w-0">
                                            <input type="checkbox" wire:model.live="categories" value="{{ $cat['industry_category'] }}" class="rounded border-gray-300 text-gray-900 focus:ring-brand-600">
                                            <span class="truncate max-w-[165px]" title="{{ $cat['industry_category'] }}">{{ $cat['industry_category'] }}</span>
                                        </span>
                                        <span class="text-text-muted text-xs font-medium">{{ $cat['count'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Sertifikasi Filter -->
                        <div class="pt-4 border-t border-border-subtle">
                            <h3 class="font-semibold text-xs uppercase text-text-secondary mb-3 tracking-wider">Sertifikasi Industri</h3>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                @foreach($certificationOptions as $cert)
                                    <label class="flex items-center justify-between text-13 text-text-secondary hover:text-text-primary cursor-pointer py-0.5">
                                        <span class="flex items-center gap-2.5 min-w-0">
                                            <input type="checkbox" wire:model.live="certifications" value="{{ $cert['name'] }}" class="rounded border-gray-300 text-gray-900 focus:ring-brand-600">
                                            <span class="truncate" title="{{ $cert['name'] }}">{{ $cert['name'] }}</span>
                                        </span>
                                        <span class="text-text-muted text-xs font-medium">{{ $cert['suppliers_count'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Metrik Wilayah -->
                        <div class="pt-4 border-t border-border-subtle">
                            <span class="text-xs font-semibold uppercase text-text-muted tracking-wider">Metrik Audit Wilayah</span>
                            <div class="mt-3 space-y-2 text-13">
                                <div class="flex justify-between text-text-secondary">
                                    <span>Rata-rata lead time</span>
                                    <span class="font-semibold text-text-primary">3,4 Minggu</span>
                                </div>
                                <div class="flex justify-between text-text-secondary">
                                    <span>Kepatuhan ISO</span>
                                    <span class="font-semibold text-brand-700">82,6%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Right Content (Header, Grid & Pagination) -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-text-primary tracking-tight">
                                @if($query)
                                    Pemasok untuk "{{ $query }}"
                                @else
                                    Pemasok terverifikasi di Indonesia
                                @endif
                            </h1>
                            <p class="text-13 text-text-secondary mt-1">
                                Menampilkan {{ $suppliers->firstItem() ?? 0 }}&ndash;{{ $suppliers->lastItem() ?? 0 }} dari {{ $suppliers->total() }} pemasok terdaftar
                            </p>
                        </div>

                        <div class="flex items-center gap-2.5 shrink-0">
                            <label for="supplier-sort" class="text-13 font-medium text-text-secondary">Urutkan:</label>
                            <select id="supplier-sort" wire:model.live="sort" class="h-10 rounded-xl border-border text-13 text-text-primary bg-white shadow-2xs focus:ring-brand-600 focus:border-brand-600 py-2 pl-3 pr-8">
                                <option value="relevance">Paling relevan</option>
                                <option value="newest">Paling baru</option>
                                <option value="company_size">Ukuran pabrik terbesar</option>
                            </select>
                        </div>
                    </div>

                    <!-- Active Filters Bar -->
                    @if(!empty($locations) || !empty($categories) || !empty($certifications) || $query)
                        <div class="flex flex-wrap items-center gap-2 mb-6 text-xs">
                            <span class="text-text-secondary font-medium">Filter aktif:</span>
                            @if($query)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-subtle border border-border rounded-lg text-text-primary font-medium">
                                    <span>Kata kunci: {{ $query }}</span>
                                    <button type="button" wire:click="$set('query', '')" aria-label="Hapus filter kata kunci" class="text-text-muted hover:text-danger">&times;</button>
                                </span>
                            @endif
                            @foreach($locations as $loc)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-subtle border border-border rounded-lg text-text-primary font-medium">
                                    <span>{{ $loc }}</span>
                                    <button type="button" wire:click="$set('locations', {{ json_encode(array_values(array_diff($locations, [$loc]))) }})" aria-label="Hapus filter {{ $loc }}" class="text-text-muted hover:text-danger">&times;</button>
                                </span>
                            @endforeach
                            @foreach($categories as $cat)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-subtle border border-border rounded-lg text-text-primary font-medium">
                                    <span>{{ $cat }}</span>
                                    <button type="button" wire:click="$set('categories', {{ json_encode(array_values(array_diff($categories, [$cat]))) }})" aria-label="Hapus filter {{ $cat }}" class="text-text-muted hover:text-danger">&times;</button>
                                </span>
                            @endforeach
                            @foreach($certifications as $cert)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-subtle border border-border rounded-lg text-text-primary font-medium">
                                    <span>{{ $cert }}</span>
                                    <button type="button" wire:click="$set('certifications', {{ json_encode(array_values(array_diff($certifications, [$cert]))) }})" aria-label="Hapus filter {{ $cert }}" class="text-text-muted hover:text-danger">&times;</button>
                                </span>
                            @endforeach
                            <button type="button" wire:click="clearFilters" class="text-xs font-medium text-danger hover:underline ml-2">Hapus semua</button>
                        </div>
                    @endif

                    <!-- Loading Skeleton State -->
                    <div wire:loading.grid
                         wire:target="query,locations,categories,certifications,sort,clearFilters,selectCategory,selectKeyword"
                         class="grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6 items-stretch mb-6">
                        @for($i = 0; $i < 4; $i++)
                            <x-supplier-card-skeleton />
                        @endfor
                    </div>

                    <!-- Suppliers Grid (Tinggi Kartu Seragam) -->
                    <div wire:loading.remove
                         wire:target="query,locations,categories,certifications,sort,clearFilters,selectCategory,selectKeyword"
                         class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6 items-stretch">
                        @forelse($suppliers as $supplier)
                            <x-supplier-card
                                :supplier="$supplier"
                                :selectable="true"
                                :selected="in_array($supplier->id, $compareIds)"
                                :limit-reached="count($compareIds) >= 4"
                            />
                        @empty
                            <div class="md:col-span-2 text-center py-16 px-6 nb-card">
                                <div class="w-12 h-12 rounded-2xl bg-surface-subtle border border-border flex items-center justify-center mx-auto mb-3 text-text-muted">
                                    <x-heroicon-o-magnifying-glass class="w-6 h-6" />
                                </div>
                                <h3 class="font-semibold text-text-primary text-base">Tidak ada pemasok yang cocok</h3>
                                <p class="text-sm text-text-secondary mt-1 max-w-md mx-auto">
                                    Coba sesuaikan kata kunci pencarian atau bersihkan beberapa filter untuk melihat pilihan pabrik lainnya.
                                </p>
                                <button type="button" wire:click="clearFilters" class="mt-5 h-10 px-4 bg-gray-900 hover:bg-black text-white text-13 font-semibold rounded-xl transition nb-focus-ring">
                                    Reset Semua Filter
                                </button>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $suppliers->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= STICKY COMPARISON TRAY ================= -->
    <x-ui.compare-tray :selected-suppliers="$this->selectedCompareSuppliers" :max-suppliers="4" />
</div>
