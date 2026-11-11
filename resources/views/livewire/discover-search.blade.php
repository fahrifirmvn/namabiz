<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if($this->isHubMode)
        <!-- ================= MODE HUB (JELAJAHI / HUB.PNG) ================= -->
        <div class="space-y-12">
            <!-- Header Hub -->
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">
                    KATALOG TERPADU / DIREKTORI B2B INDONESIA
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mt-1">
                    Cari pemasok
                </h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">
                    Telusuri berdasarkan kategori atau mulai dari pencarian Anda.
                </p>
            </div>

            <!-- Search Bar in Hub -->
            <div class="max-w-3xl">
                <div class="relative flex items-center shadow-sm rounded-2xl bg-white border border-gray-200 p-2 focus-within:ring-2 focus-within:ring-black transition">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 ml-3 shrink-0" />
                    <input type="text"
                           wire:model.live.debounce.300ms="query"
                           placeholder="Cari pemasok atau bahan baku"
                           class="w-full pl-3 pr-4 py-2 text-sm text-gray-900 placeholder-gray-400 bg-transparent border-none focus:outline-none focus:ring-0">
                    <div class="mr-2 hidden sm:block">
                        <kbd class="px-2 py-1 text-xs text-gray-400 bg-gray-50 border border-gray-200 rounded font-mono">/</kbd>
                    </div>
                </div>

                <!-- Recent Searches -->
                <div class="mt-4 flex flex-wrap items-center justify-between gap-2 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-gray-400 font-medium">Pencarian terakhir:</span>
                        <button type="button" wire:click="selectKeyword('kardus bergelombang')" class="inline-flex items-center gap-1 px-3 py-1 bg-white hover:bg-gray-100 border border-gray-200 rounded-full text-gray-700 transition">
                            <x-heroicon-o-clock class="w-3.5 h-3.5 text-gray-400" /> kardus bergelombang
                        </button>
                        <button type="button" wire:click="selectKeyword('resin PET')" class="inline-flex items-center gap-1 px-3 py-1 bg-white hover:bg-gray-100 border border-gray-200 rounded-full text-gray-700 transition">
                            <x-heroicon-o-clock class="w-3.5 h-3.5 text-gray-400" /> resin PET
                        </button>
                        <button type="button" wire:click="selectKeyword('mesin pengemas')" class="inline-flex items-center gap-1 px-3 py-1 bg-white hover:bg-gray-100 border border-gray-200 rounded-full text-gray-700 transition">
                            <x-heroicon-o-clock class="w-3.5 h-3.5 text-gray-400" /> mesin pengemas
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kategori Industri Grid -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Kategori industri</h2>
                    <span class="text-xs font-semibold text-gray-400">{{ count($hubCategories) }} Sektor Terdaftar</span>
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

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @forelse($hubCategories as $cat)
                        <button type="button"
                                wire:click="selectCategory(@js($cat['name']))"
                                class="p-5 rounded-2xl bg-white hover:bg-gray-50 border border-gray-200 transition text-left group flex items-center justify-between shadow-2xs">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-700 group-hover:scale-105 transition-transform shrink-0">
                                    <x-dynamic-component :component="'heroicon-o-' . ($categoryIcons[$cat['name']] ?? 'building-office-2')" class="w-5 h-5 text-gray-700" />
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-gray-900 text-sm group-hover:text-black truncate">{{ $cat['name'] }}</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $cat['count'] }} pemasok</p>
                                </div>
                            </div>
                            <x-heroicon-s-chevron-right class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition-transform shrink-0" />
                        </button>
                    @empty
                        <div class="col-span-full py-8 text-center text-xs text-gray-400 bg-white rounded-2xl border border-dashed border-gray-200">
                            Belum ada kategori industri yang terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Rekomendasi "Untuk Anda" -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Untuk Anda</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Berdasarkan minat industri manufaktur & kemasan terverifikasi.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($this->recommendedSuppliers as $rec)
                        <x-supplier-card :supplier="$rec" />
                    @endforeach
                </div>
            </div>

            <!-- Banner Bantuan & Konsultasi Langsung -->
            <div class="bg-surface rounded-2xl border border-gray-200 p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 flex items-center justify-center text-emerald-600 shadow-2xs shrink-0">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Tidak menemukan spesifikasi bahan baku yang Anda butuhkan?</h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Konsultasikan kebutuhan pasokan industri Anda langsung dengan tim kurator Namabiz.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('discover.index', ['q' => 'Kemasan']) }}" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                        Telusuri Kategori
                    </a>
                    <a href="https://wa.me/6281234567890?text={{ urlencode('Halo Tim Namabiz, saya mencari rekomendasi pemasok untuk kebutuhan industri.') }}" target="_blank" rel="noopener noreferrer" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition inline-flex items-center gap-1.5">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4" />
                        <span>Hubungi Kurator WA</span>
                    </a>
                </div>
            </div>
        </div>

    @else
        <!-- ================= MODE HASIL PENCARIAN (HASIL (PREMIUM).PNG) ================= -->
        <div>
            <!-- Breadcrumbs -->
            <nav class="text-xs text-gray-500 mb-4 flex items-center gap-2">
                <a href="{{ route('discover.index') }}" wire:click.prevent="clearFilters" class="hover:text-gray-900">Direktori</a>
                <span>/</span>
                <span>Manufaktur & Kemasan</span>
                <span>/</span>
                <span class="text-gray-900 font-semibold">Indonesia</span>
            </nav>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Left Sidebar Filters -->
                <aside class="lg:w-72 shrink-0">
                    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-2xs space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                                <x-heroicon-o-adjustments-horizontal class="w-4 h-4 text-gray-500" /> Parameter Filter
                            </h2>
                            <button wire:click="clearFilters" class="text-xs text-gray-500 hover:text-black underline font-medium">Reset</button>
                        </div>

                        <!-- Lokasi Filter -->
                        <div>
                            <h3 class="font-bold text-xs uppercase text-gray-700 mb-3 tracking-wider">Lokasi Geografis</h3>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                @foreach($locationOptions as $loc)
                                    <label class="flex items-center justify-between text-xs text-gray-700 hover:text-gray-900 cursor-pointer">
                                        <span class="flex items-center gap-2">
                                            <input type="checkbox" wire:model.live="locations" value="{{ $loc['country'] }}" class="rounded border-gray-300 text-black focus:ring-black">
                                            <span>{{ $loc['country'] }}</span>
                                        </span>
                                        <span class="text-gray-400 text-2xs">{{ $loc['count'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Kategori Filter -->
                        <div class="pt-4 border-t border-gray-100">
                            <h3 class="font-bold text-xs uppercase text-gray-700 mb-3 tracking-wider">Kategori Industri</h3>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                @foreach($categoryOptions as $cat)
                                    <label class="flex items-center justify-between text-xs text-gray-700 hover:text-gray-900 cursor-pointer">
                                        <span class="flex items-center gap-2">
                                            <input type="checkbox" wire:model.live="categories" value="{{ $cat['industry_category'] }}" class="rounded border-gray-300 text-black focus:ring-black">
                                            <span class="truncate max-w-[150px]">{{ $cat['industry_category'] }}</span>
                                        </span>
                                        <span class="text-gray-400 text-2xs">{{ $cat['count'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Sertifikasi Filter -->
                        <div class="pt-4 border-t border-gray-100">
                            <h3 class="font-bold text-xs uppercase text-gray-700 mb-3 tracking-wider">Sertifikasi Industri</h3>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                @foreach($certificationOptions as $cert)
                                    <label class="flex items-center justify-between text-xs text-gray-700 hover:text-gray-900 cursor-pointer">
                                        <span class="flex items-center gap-2">
                                            <input type="checkbox" wire:model.live="certifications" value="{{ $cert['name'] }}" class="rounded border-gray-300 text-black focus:ring-black">
                                            <span>{{ $cert['name'] }}</span>
                                        </span>
                                        <span class="text-gray-400 text-2xs">{{ $cert['suppliers_count'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Metrik Wilayah -->
                        <div class="pt-4 border-t border-gray-100">
                            <span class="text-2xs font-bold uppercase text-gray-400 tracking-wider">METRIK AUDIT WILAYAH</span>
                            <div class="mt-3 space-y-2 text-xs">
                                <div class="flex justify-between text-gray-600">
                                    <span>Rata-rata lead time</span>
                                    <span class="font-semibold text-gray-900">3,4 Minggu</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Kepatuhan ISO</span>
                                    <span class="font-semibold text-emerald-700">82,6%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Right Content (Header, Grid & Pagination) -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                                @if($query)
                                    Pemasok untuk "{{ $query }}"
                                @else
                                    Pemasok terverifikasi di Indonesia
                                @endif
                            </h1>
                            <p class="text-xs text-gray-500 mt-1">
                                Menampilkan {{ $suppliers->firstItem() ?? 0 }}-{{ $suppliers->lastItem() ?? 0 }} dari {{ $suppliers->total() }} pemasok terverifikasi
                            </p>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <label for="supplier-sort" class="text-xs font-medium text-gray-500">Urutkan:</label>
                            <select id="supplier-sort" wire:model.live="sort" class="rounded-xl border-gray-200 text-xs text-gray-800 bg-white shadow-2xs focus:ring-black focus:border-black py-2 pl-3 pr-8">
                                <option value="relevance">Paling relevan</option>
                                <option value="newest">Paling baru</option>
                                <option value="company_size">Ukuran pabrik terbesar</option>
                            </select>
                        </div>
                    </div>

                    <!-- Active Filters Bar -->
                    @if(!empty($locations) || !empty($categories) || !empty($certifications) || $query)
                        <div class="flex flex-wrap items-center gap-2 mb-6 text-xs">
                            <span class="text-gray-400 font-medium">Filter aktif:</span>
                            @if($query)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 rounded-lg text-gray-800">
                                    Kata kunci: {{ $query }}
                                    <button type="button" wire:click="$set('query', '')" class="hover:text-red-600">&times;</button>
                                </span>
                            @endif
                            @foreach($locations as $loc)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 rounded-lg text-gray-800">
                                    {{ $loc }}
                                    <button type="button" wire:click="$set('locations', {{ json_encode(array_values(array_diff($locations, [$loc]))) }})" class="hover:text-red-600">&times;</button>
                                </span>
                            @endforeach
                            @foreach($categories as $cat)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 rounded-lg text-gray-800">
                                    {{ $cat }}
                                    <button type="button" wire:click="$set('categories', {{ json_encode(array_values(array_diff($categories, [$cat]))) }})" class="hover:text-red-600">&times;</button>
                                </span>
                            @endforeach
                            @foreach($certifications as $cert)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 rounded-lg text-gray-800">
                                    {{ $cert }}
                                    <button type="button" wire:click="$set('certifications', {{ json_encode(array_values(array_diff($certifications, [$cert]))) }})" class="hover:text-red-600">&times;</button>
                                </span>
                            @endforeach
                            <button type="button" wire:click="clearFilters" class="text-xs text-red-600 hover:underline ml-2">Hapus semua</button>
                        </div>
                    @endif

                    <!-- Suppliers Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($suppliers as $supplier)
                            <div class="relative">
                                <!-- Checkbox Bandingkan -->
                                <div class="absolute top-4 right-14 z-10">
                                    <label class="inline-flex items-center gap-1.5 text-xs text-gray-600 font-medium cursor-pointer bg-white/90 px-2.5 py-1 rounded-lg border border-gray-200 shadow-2xs hover:bg-gray-50">
                                        <input type="checkbox"
                                               wire:click="toggleCompare({{ $supplier->id }})"
                                               {{ in_array($supplier->id, $compareIds) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-black focus:ring-black w-3.5 h-3.5">
                                        <span>Bandingkan</span>
                                    </label>
                                </div>
                                <x-supplier-card :supplier="$supplier" />
                            </div>
                        @empty
                            <div class="md:col-span-2 text-center py-16 bg-white rounded-2xl border border-gray-200">
                                <x-heroicon-o-magnifying-glass class="w-10 h-10 text-gray-300 mx-auto mb-3" />
                                <h3 class="font-bold text-gray-900 text-base">Tidak ada pemasok yang cocok</h3>
                                <p class="text-xs text-gray-500 mt-1">Coba sesuaikan kata kunci atau bersihkan parameter filter Anda.</p>
                                <button type="button" wire:click="clearFilters" class="mt-4 px-4 py-2 bg-black text-white text-xs font-semibold rounded-xl">
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

    <!-- ================= FLOATING STICKY COMPARISON BAR ================= -->
    @if(count($compareIds) > 0)
        <div class="fixed bottom-0 inset-x-0 z-50 bg-black text-white py-3.5 px-4 shadow-2xl border-t border-gray-800 transition-all">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="text-xs font-bold text-emerald-400">
                        {{ count($compareIds) }} dari 4 pemasok dipilih:
                    </span>
                    @foreach($this->selectedCompareSuppliers as $selected)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-gray-800 text-xs font-medium text-white border border-gray-700">
                            <span class="truncate max-w-[120px]">{{ $selected->name }}</span>
                            <button type="button" wire:click="toggleCompare({{ $selected->id }})" class="text-gray-400 hover:text-white">&times;</button>
                        </span>
                    @endforeach
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <button type="button" wire:click="clearCompare" class="text-xs text-gray-400 hover:text-white underline">
                        Bersihkan pilihan
                    </button>
                    <button type="button" wire:click="goToCompare" class="px-5 py-2 rounded-xl bg-white text-black font-bold text-xs hover:bg-gray-100 transition shadow-sm">
                        Bandingkan ({{ count($compareIds) }}) &rarr;
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
