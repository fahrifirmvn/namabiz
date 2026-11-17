<x-app-layout title="Beranda Akun" robots="noindex, nofollow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- 1. Header & Greeting -->
        <div>
            <div class="text-xs font-semibold text-brand-700 uppercase tracking-widest mb-1.5">
                PORTAL PENGADAAN TERPADU
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Selamat datang kembali, {{ auth()->user()->name }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Lanjutkan pekerjaan Anda atau mulai pencarian baru.
            </p>
        </div>

        <!-- 2. Search & Preferences Bar -->
        <div class="nb-card p-5">
            <form action="{{ route('discover.index') }}" method="GET" class="flex gap-3">
                <div class="relative flex-1">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input type="text"
                           name="q"
                           placeholder="Cari pemasok atau bahan baku"
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-surface border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-600 focus:border-brand-600 transition">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white hover:bg-black rounded-xl text-sm font-semibold transition shrink-0 flex items-center gap-1.5 shadow-2xs nb-focus-ring">
                    <span>Cari</span>
                    <span aria-hidden="true">&rarr;</span>
                </button>
            </form>

            <div class="mt-3.5 flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-gray-400 font-semibold uppercase tracking-wider text-2xs">PREFERENSI ANDA:</span>
                    @foreach(($preferredCategories ?? collect()) as $pref)
                        <a href="{{ route('discover.index', ['category' => $pref->name]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-surface-subtle hover:bg-brand-50 hover:text-brand-800 border border-gray-200/70 hover:border-brand-200 text-gray-700 font-medium transition-colors">
                            {{ $pref->name }} <x-heroicon-m-adjustments-vertical class="w-3.5 h-3.5 text-gray-400" />
                        </a>
                    @endforeach
                </div>
                <a href="{{ route('discover.index') }}" class="font-semibold text-gray-700 hover:text-brand-700 transition-colors">
                    Lihat semua kategori &rarr;
                </a>
            </div>
        </div>

        <!-- 3. Metrics Summary (3 Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 tabular-nums">
            <!-- Pemasok Tersimpan -->
            <a href="{{ route('saved.index') }}" class="nb-card-interactive hover:border-brand-500/40 p-6 flex flex-col justify-between group nb-focus-ring">
                <div class="flex items-start justify-between">
                    <span class="text-xs font-semibold text-gray-500 group-hover:text-gray-900 transition-colors">Pemasok tersimpan</span>
                    <x-heroicon-m-arrow-up-right class="w-4 h-4 text-gray-400 group-hover:text-brand-600 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all" />
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-extrabold text-gray-900">{{ $savedCount ?? count($savedSuppliers) }}</span>
                    <p class="text-xs text-gray-400 mt-1">Rekanan dalam koleksi Anda</p>
                </div>
            </a>

            <!-- Perbandingan Aktif -->
            @php
                $compareList = $compareSuppliers ?? collect();
                $compareCount = $compareList->count();
                $compareRoute = route('compare.index', $compareCount > 0 ? ['suppliers' => $compareList->pluck('id')->all()] : []);
            @endphp
            <a href="{{ $compareRoute }}" class="nb-card-interactive hover:border-brand-500/40 p-6 flex flex-col justify-between group nb-focus-ring">
                <div class="flex items-start justify-between">
                    <span class="text-xs font-semibold text-gray-500 group-hover:text-gray-900 transition-colors">Perbandingan aktif</span>
                    <x-heroicon-m-arrow-up-right class="w-4 h-4 text-gray-400 group-hover:text-brand-600 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all" />
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-extrabold text-gray-900">{{ $compareCount > 0 ? 1 : 0 }}</span>
                    <p class="text-xs text-gray-400 mt-1">{{ $compareCount }} kandidat siap dibandingkan</p>
                </div>
            </a>

            <!-- Proyek Pengadaan -->
            <a href="{{ route('saved.index') }}" class="nb-card-interactive hover:border-brand-500/40 p-6 flex flex-col justify-between group nb-focus-ring">
                <div class="flex items-start justify-between">
                    <span class="text-xs font-semibold text-gray-500 group-hover:text-gray-900 transition-colors">Proyek pengadaan</span>
                    <x-heroicon-m-arrow-up-right class="w-4 h-4 text-gray-400 group-hover:text-brand-600 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all" />
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-extrabold text-gray-900">{{ $collectionsCount ?? 0 }}</span>
                    <p class="text-xs text-gray-400 mt-1">Koleksi tender tersusun</p>
                </div>
            </a>
        </div>

        <!-- 4. Perbandingan Berjalan Card -->
        @if($compareCount > 0)
            <div class="nb-card p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4 min-w-0 flex-1">
                    <div class="flex -space-x-2 shrink-0">
                        @foreach($compareList->take(3) as $idx => $cmp)
                            <span class="w-9 h-9 rounded-xl {{ $idx === 0 ? 'bg-gray-900' : ($idx === 1 ? 'bg-gray-800' : 'bg-gray-600') }} text-white font-bold flex items-center justify-center text-xs border-2 border-white shadow-2xs">
                                {{ strtoupper(substr($cmp->name, 0, 2)) }}
                            </span>
                        @endforeach
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-2xs font-semibold uppercase text-gray-400 tracking-wider block">PERBANDINGAN BERJALAN &bull; {{ $compareCount }} PEMASOK</span>
                        <h3 class="text-sm font-bold text-gray-900 mt-0.5 truncate" title="{{ $compareList->pluck('name')->implode(', ') }}">
                            {{ $compareList->pluck('name')->implode(', ') }}
                        </h3>
                    </div>
                </div>

                <a href="{{ $compareRoute }}" class="px-4 py-2 bg-surface-subtle hover:bg-gray-200/80 border border-gray-200/70 text-gray-900 text-xs font-semibold rounded-xl transition text-center shrink-0 nb-focus-ring">
                    Buka perbandingan &rarr;
                </a>
            </div>
        @endif

        <!-- 5. Two Columns (Main Activities & Sidebar Widgets) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Main Column (2 Spans) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Baru Dilihat -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-gray-900">Baru Anda lihat</h2>
                            <span class="text-xs bg-surface-subtle border border-gray-200/70 text-gray-600 px-2.5 py-0.5 rounded-full font-medium tabular-nums">{{ count($recentlyViewed) }} entitas</span>
                        </div>
                        <a href="{{ route('discover.index') }}" class="text-xs font-semibold text-gray-600 hover:text-brand-700 transition-colors">
                            Lihat direktori &rarr;
                        </a>
                    </div>

                    @if($recentlyViewed->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @foreach($recentlyViewed as $recent)
                                <div class="nb-card-interactive hover:border-brand-500/35 p-4 relative group flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-start justify-between gap-2">
                                            <x-ui.avatar size="sm" :name="$recent->name" :logo="$recent->logo" />
                                            @if($recent->is_verified)
                                                <span class="inline-flex items-center gap-1 text-[11px] bg-gray-900 text-white px-2 py-0.5 rounded-md font-medium shrink-0">
                                                    <x-heroicon-s-check class="w-3 h-3 text-emerald-400" /> Terverifikasi
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="font-bold text-sm text-gray-900 mt-3 truncate" title="{{ $recent->name }}">
                                            <a href="{{ route('suppliers.show', $recent->slug) }}" class="hover:underline nb-focus-ring rounded">{{ $recent->name }}</a>
                                        </h3>
                                    </div>
                                    <p class="text-2xs text-gray-500 mt-2 truncate">{{ \App\Support\SupplierFormat::category($recent->industry_category) }} &bull; {{ $recent->city ?? $recent->country }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="nb-card p-6 text-center text-xs text-gray-500">
                            Belum ada profil pemasok yang Anda buka pada sesi ini. <a href="{{ route('discover.index') }}" class="font-semibold text-brand-700 hover:underline">Jelajahi direktori</a>
                        </div>
                    @endif
                </div>

                <!-- Pemasok Tersimpan List -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Pemasok tersimpan</h2>
                            <p class="text-xs text-gray-500">Daftar rekanan yang telah Anda tandai untuk dievaluasi.</p>
                        </div>
                        <a href="{{ route('saved.index') }}" class="text-xs font-semibold text-gray-900 hover:text-brand-700 transition-colors">
                            Lihat semua ({{ $savedCount ?? count($savedSuppliers) }}) &rarr;
                        </a>
                    </div>

                    <div class="nb-card divide-y divide-gray-100 overflow-hidden">
                        @forelse($savedSuppliers as $saved)
                            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-surface/60 transition-colors">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <x-ui.avatar size="sm" :name="$saved->name" :logo="$saved->logo" />
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="font-bold text-sm text-gray-900 truncate">
                                                <a href="{{ route('suppliers.show', $saved->slug) }}" class="hover:underline nb-focus-ring rounded">{{ $saved->name }}</a>
                                            </h3>
                                            @if($saved->is_verified)
                                                <span class="inline-flex items-center gap-1 text-[11px] bg-gray-900 text-white px-1.5 py-0.5 rounded font-medium shrink-0">
                                                    <x-heroicon-s-check class="w-3 h-3 text-emerald-400" /> Terverifikasi
                                                </span>
                                            @endif
                                            <span class="text-xs text-gray-400 hidden sm:inline truncate">&bull; {{ $saved->city ?? $saved->country }}</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1 mt-1.5">
                                            @foreach($saved->tags->take(2) as $tg)
                                                <x-ui.chip class="h-5 text-[11px]">{{ $tg->tag_name }}</x-ui.chip>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('suppliers.show', $saved->slug) }}"
                                       class="px-3.5 py-1.5 bg-surface-subtle hover:bg-gray-200/80 border border-gray-200/70 text-gray-800 text-xs font-semibold rounded-xl transition nb-focus-ring">
                                        Lihat
                                    </a>
                                    @if($saved->email)
                                    <a href="mailto:{{ $saved->email }}?subject=Hubungi%20{{ urlencode($saved->name) }}"
                                       class="px-3.5 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-xl transition nb-focus-ring">
                                        Hubungi
                                    </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs text-gray-500">
                                Belum ada pemasok tersimpan. <a href="{{ route('discover.index') }}" class="font-semibold text-brand-700 hover:underline">Jelajahi direktori</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column (1 Span) -->
            <aside class="space-y-6">
                <!-- Upgrade Upsell Widget (Solid Charcoal Contrast) -->
                @unless(auth()->user()->isPremium())
                    <div class="bg-gray-900 text-white rounded-2xl border border-gray-800 p-6 shadow-card">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-2xs font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-gray-800 border border-gray-700 text-gray-200">
                                PAKET GRATIS
                            </span>
                            <x-heroicon-s-shield-check class="w-5 h-5 text-emerald-400" />
                        </div>
                        <h3 class="text-base font-extrabold text-white">Buka data pemasok lengkap</h3>
                        <p class="text-xs text-gray-300 mt-1 leading-relaxed">
                            Akses parameter analitis mendalam untuk memitigasi risiko pengadaan organisasi Anda.
                        </p>

                        <ul class="mt-5 space-y-3 text-xs text-gray-200">
                            <li class="flex items-center gap-2">
                                <x-heroicon-s-lock-closed class="w-4 h-4 text-brand-500 shrink-0" />
                                <span>Kapasitas, MOQ, dan lead time</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <x-heroicon-s-lock-closed class="w-4 h-4 text-brand-500 shrink-0" />
                                <span>Semua sertifikasi mutu terakreditasi</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <x-heroicon-s-lock-closed class="w-4 h-4 text-brand-500 shrink-0" />
                                <span>Pustaka dokumen lengkap &amp; audit</span>
                            </li>
                        </ul>

                        <a href="{{ route('upgrade') }}" class="mt-6 block text-center py-2.5 px-4 bg-brand-600 text-white hover:bg-brand-500 font-semibold text-xs rounded-xl shadow-xs transition nb-focus-ring">
                            Upgrade ke Premium &rarr;
                        </a>
                    </div>
                @endunless

                <!-- Pemasok Terbaru / Terverifikasi Widget -->
                <div class="nb-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-900">Pemasok unggulan katalog</h3>
                        <x-heroicon-o-building-office-2 class="w-4 h-4 text-brand-600" />
                    </div>
                    <p class="text-2xs text-gray-500 mb-4">
                        Pemasok terkurasi dan pembaruan terbaru dari direktori.
                    </p>

                    <div class="space-y-3.5">
                        @forelse($recommended as $rec)
                            <div class="flex items-center justify-between gap-3 text-xs">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-gray-900 truncate">
                                        <a href="{{ route('suppliers.show', $rec->slug) }}" class="hover:underline nb-focus-ring rounded">{{ $rec->name }}</a>
                                    </h4>
                                    <p class="text-2xs text-gray-400 mt-0.5 truncate">{{ $rec->city ?? $rec->country }} &bull; {{ \App\Support\SupplierFormat::category($rec->industry_category) }}</p>
                                </div>
                                <a href="{{ route('suppliers.show', $rec->slug) }}" class="px-3 py-1 bg-surface-subtle hover:bg-brand-50 hover:text-brand-800 border border-gray-200/70 hover:border-brand-200 text-gray-800 text-2xs font-semibold rounded-lg shrink-0 transition-colors">
                                    Lihat
                                </a>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500">Belum ada pemasok terdaftar di katalog.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Verifikasi & Transparansi Card -->
                <div class="bg-white rounded-2xl border border-gray-200 border-l-4 border-l-brand-600 p-5 flex items-start gap-3 text-xs text-gray-600 shadow-2xs">
                    <x-heroicon-s-shield-check class="w-5 h-5 text-brand-600 shrink-0 mt-0.5" />
                    <p class="leading-relaxed text-2xs">
                        Status Terverifikasi dan catatan sertifikasi ditampilkan sesuai kurasi profil katalog. Selalu konfirmasikan dokumen penawaran langsung dengan pemasok.
                    </p>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
