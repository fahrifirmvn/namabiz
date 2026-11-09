<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- 1. Header & Greeting -->
        <div>
            <div class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-widest mb-1">
                <span class="w-2 h-2 rounded-full bg-black"></span>
                <span>PORTAL PENGADAAN TERPADU</span>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Selamat datang kembali, {{ auth()->user()->name }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Lanjutkan pekerjaan Anda atau mulai pencarian baru.
            </p>
        </div>

        <!-- 2. Search & Preferences Bar -->
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs">
            <form action="{{ route('discover.index') }}" method="GET" class="flex gap-3">
                <div class="relative flex-1">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input type="text"
                           name="q"
                           placeholder="Cari pemasok atau bahan baku"
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-black focus:border-black transition">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-black text-white hover:bg-gray-800 rounded-xl text-sm font-semibold transition shrink-0 flex items-center gap-1.5">
                    <span>Cari</span>
                    <span aria-hidden="true">&rarr;</span>
                </button>
            </form>

            <div class="mt-3.5 flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-gray-400 font-semibold uppercase tracking-wider text-2xs">PREFERENSI ANDA:</span>
                    <a href="{{ route('discover.index', ['category' => 'Kemasan']) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium">
                        Kemasan <x-heroicon-m-adjustments-vertical class="w-3.5 h-3.5 text-gray-400" />
                    </a>
                    <a href="{{ route('discover.index', ['category' => 'Resin']) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium">
                        Resin <x-heroicon-m-adjustments-vertical class="w-3.5 h-3.5 text-gray-400" />
                    </a>
                </div>
                <a href="{{ route('discover.index') }}" class="font-semibold text-gray-700 hover:text-black">
                    Lihat semua kategori &rarr;
                </a>
            </div>
        </div>

        <!-- 3. Metrics Summary (3 Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Pemasok Tersimpan -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <span class="text-xs font-semibold text-gray-500">Pemasok tersimpan</span>
                    <x-heroicon-m-arrow-up-right class="w-4 h-4 text-gray-400" />
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-extrabold text-gray-900">{{ count($savedSuppliers) }}</span>
                    <p class="text-xs text-gray-400 mt-1">{{ count($savedSuppliers) }} berkas terverifikasi</p>
                </div>
            </div>

            <!-- Perbandingan Aktif -->
            <a href="{{ route('compare.index') }}" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs flex flex-col justify-between hover:border-black transition group">
                <div class="flex items-start justify-between">
                    <span class="text-xs font-semibold text-gray-500 group-hover:text-black">Perbandingan aktif</span>
                    <x-heroicon-m-arrow-up-right class="w-4 h-4 text-gray-400 group-hover:text-black" />
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-extrabold text-gray-900">1</span>
                    <p class="text-xs text-gray-400 mt-1">3 kandidat bersaing</p>
                </div>
            </a>

            <!-- Pesan Belum Dibaca -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <span class="text-xs font-semibold text-gray-500">Pesan belum dibaca</span>
                    <x-heroicon-m-arrow-up-right class="w-4 h-4 text-gray-400" />
                </div>
                <div class="mt-4">
                    <div class="flex items-center gap-2">
                        <span class="text-3xl font-extrabold text-gray-900">2</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Respons RFQ masuk</p>
                </div>
            </div>
        </div>

        <!-- 4. Perbandingan Berjalan Card -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex -space-x-2 shrink-0">
                    <span class="w-9 h-9 rounded-xl bg-black text-white font-bold flex items-center justify-center text-xs border-2 border-white">AP</span>
                    <span class="w-9 h-9 rounded-xl bg-gray-800 text-white font-bold flex items-center justify-center text-xs border-2 border-white">NP</span>
                    <span class="w-9 h-9 rounded-xl bg-gray-600 text-white font-bold flex items-center justify-center text-xs border-2 border-white">IT</span>
                </div>
                <div>
                    <span class="text-2xs font-semibold uppercase text-gray-400 tracking-wider">PERBANDINGAN BERJALAN &bull; Diperbarui baru saja</span>
                    <h3 class="text-sm font-bold text-gray-900 mt-0.5">Atlas Packaging, Nusantara Plastics, Indo-Tech Solutions</h3>
                </div>
            </div>

            <a href="{{ route('compare.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 text-xs font-semibold rounded-xl transition text-center shrink-0">
                Buka perbandingan &rarr;
            </a>
        </div>

        <!-- 5. Two Columns (Main Activities & Sidebar Widgets) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Main Column (2 Spans) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Baru Dilihat -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-gray-900">Baru dilihat</h2>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full font-medium">3 entitas</span>
                        </div>
                        <span class="text-xs text-gray-400">Aktivitas 7 hari terakhir</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($recentlyViewed as $recent)
                            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-2xs hover:shadow-xs transition relative">
                                <div class="flex items-start justify-between">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 font-bold text-gray-800 flex items-center justify-center text-sm">
                                        {{ strtoupper(substr($recent->name, 0, 2)) }}
                                    </div>
                                    <x-heroicon-o-bookmark class="w-4 h-4 text-gray-400" />
                                </div>
                                <h3 class="font-bold text-sm text-gray-900 mt-3 truncate">
                                    <a href="{{ route('suppliers.show', $recent->slug) }}" class="hover:underline">{{ $recent->name }}</a>
                                </h3>
                                @if($recent->is_verified)
                                    <span class="inline-flex items-center gap-1 text-2xs bg-black text-white px-2 py-0.5 rounded font-medium mt-1">
                                        <x-heroicon-s-check class="w-3 h-3 text-emerald-400" /> Terverifikasi
                                    </span>
                                @endif
                                <p class="text-2xs text-gray-500 mt-2 truncate">{{ $recent->industry_category }} &bull; {{ $recent->city ?? 'Jakarta' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pemasok Tersimpan List -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Pemasok tersimpan</h2>
                            <p class="text-xs text-gray-500">Daftar rekanan yang telah Anda tandai untuk dievaluasi.</p>
                        </div>
                        <a href="{{ route('discover.index') }}" class="text-xs font-semibold text-black hover:underline">
                            Lihat semua ({{ count($savedSuppliers) }}) &rarr;
                        </a>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 divide-y divide-gray-100 shadow-xs">
                        @foreach($savedSuppliers as $saved)
                            <div class="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-black text-white font-bold flex items-center justify-center text-sm shrink-0">
                                        {{ strtoupper(substr($saved->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-bold text-sm text-gray-900 truncate">
                                                <a href="{{ route('suppliers.show', $saved->slug) }}" class="hover:underline">{{ $saved->name }}</a>
                                            </h3>
                                            @if($saved->is_verified)
                                                <span class="inline-flex items-center gap-1 text-2xs bg-black text-white px-1.5 py-0.5 rounded font-medium shrink-0">
                                                    <x-heroicon-s-check class="w-3 h-3 text-emerald-400" /> Terverifikasi
                                                </span>
                                            @endif
                                            <span class="text-xs text-gray-400 hidden sm:inline">&bull; {{ $saved->city ?? $saved->country }}</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($saved->tags->take(2) as $tg)
                                                <span class="text-2xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">{{ $tg->tag_name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="mailto:info@namabiz.com?subject=Hubungi%20{{ urlencode($saved->name) }}"
                                       class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-semibold rounded-xl transition">
                                        Hubungi
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column (1 Span) -->
            <aside class="space-y-6">
                <!-- Upgrade Upsell Widget -->
                @unless(auth()->user()->isPremium())
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs relative">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-2xs font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-700">
                                PAKET GRATIS
                            </span>
                            <x-heroicon-s-sparkles class="w-5 h-5 text-amber-500" />
                        </div>
                        <h3 class="text-base font-extrabold text-gray-900">Buka data pemasok lengkap</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Akses parameter analitis mendalam untuk memitigasi risiko pengadaan organisasi Anda.
                        </p>

                        <ul class="mt-5 space-y-3 text-xs text-gray-700">
                            <li class="flex items-center gap-2">
                                <x-heroicon-s-lock-closed class="w-4 h-4 text-gray-400" />
                                <span>Kapasitas, MOQ, dan lead time</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <x-heroicon-s-lock-closed class="w-4 h-4 text-gray-400" />
                                <span>Semua sertifikasi mutu terakreditasi</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <x-heroicon-s-lock-closed class="w-4 h-4 text-gray-400" />
                                <span>Pustaka dokumen lengkap & audit</span>
                            </li>
                        </ul>

                        <a href="{{ route('upgrade') }}" class="mt-6 block text-center py-2.5 px-4 bg-black text-white hover:bg-gray-800 font-semibold text-xs rounded-xl shadow-xs transition">
                            Upgrade ke Premium &rarr;
                        </a>
                    </div>
                @endunless

                <!-- Rekomendasi untuk Anda Widget -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-900">Rekomendasi untuk Anda</h3>
                        <x-heroicon-o-sparkles class="w-4 h-4 text-gray-400" />
                    </div>
                    <p class="text-2xs text-gray-500 mb-4">
                        Disesuaikan dengan pencarian kemasan dan resin industri Anda.
                    </p>

                    <div class="space-y-3.5">
                        @foreach($recommended as $rec)
                            <div class="flex items-center justify-between gap-3 text-xs">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-gray-900 truncate">
                                        <a href="{{ route('suppliers.show', $rec->slug) }}" class="hover:underline">{{ $rec->name }}</a>
                                    </h4>
                                    <p class="text-2xs text-gray-400 mt-0.5 truncate">{{ $rec->city ?? 'Bekasi' }} &bull; {{ $rec->industry_category }}</p>
                                </div>
                                <a href="{{ route('suppliers.show', $rec->slug) }}" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-800 text-2xs font-semibold rounded-lg shrink-0">
                                    Lihat
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between text-2xs text-gray-500">
                        <span>Akurasi rekomendasi</span>
                        <span class="font-bold text-emerald-700">94% relevan</span>
                    </div>
                </div>

                <!-- Verifikasi & Trust Card -->
                <div class="bg-surface rounded-2xl border border-gray-200 p-5 flex items-start gap-3 text-xs text-gray-600">
                    <x-heroicon-s-shield-check class="w-5 h-5 text-gray-900 shrink-0 mt-0.5" />
                    <p class="leading-relaxed text-2xs">
                        Semua rekanan telah melalui verifikasi legalitas NIB dan audit fasilitas terpadu oleh tim kurator Namabiz.
                    </p>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
