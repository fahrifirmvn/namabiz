<x-app-layout>
    <!-- 1. Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-16 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Badge Pill -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-700 mb-6">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>INFRASTRUKTUR PENGADAAN B2B INDONESIA</span>
            </div>

            <!-- Main Heading -->
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight max-w-4xl mx-auto leading-tight">
                Temukan pemasok yang tepat, lebih cepat.
            </h1>

            <p class="mt-4 text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                Cari produsen dan distributor terverifikasi di seluruh Indonesia, bandingkan secara berdampingan, dan hubungi langsung.
            </p>

            <!-- Search Bar -->
            <div class="mt-8 max-w-2xl mx-auto">
                <form action="{{ route('discover.index') }}" method="GET" class="relative flex items-center shadow-lg rounded-2xl bg-white border border-gray-200 p-2 focus-within:ring-2 focus-within:ring-black transition">
                    <x-heroicon-o-magnifying-glass class="w-6 h-6 text-gray-400 ml-3 shrink-0" />
                    <input type="text"
                           name="q"
                           placeholder="Coba 'kardus bergelombang' atau 'resin PET'"
                           class="w-full pl-3 pr-4 py-2.5 text-base text-gray-900 placeholder-gray-400 bg-transparent border-none focus:outline-none focus:ring-0">
                    <button type="submit" class="shrink-0 bg-black text-white hover:bg-gray-800 px-6 py-2.5 rounded-xl font-medium text-sm transition">
                        Cari
                    </button>
                </form>

                <!-- Popular Keywords -->
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2 text-xs">
                    <span class="text-gray-400 font-medium">Populer:</span>
                    @foreach(['Kemasan', 'Resin', 'Mesin', 'Tekstil', 'Bahan makanan'] as $chip)
                        <a href="{{ route('discover.index', ['q' => $chip]) }}" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md font-medium transition">
                            {{ $chip }}
                        </a>
                    @endforeach
                </div>

                <p class="mt-2 text-xs text-gray-400">Gratis untuk memulai. Tanpa kartu kredit.</p>

                <!-- Metric Counters -->
                <div class="mt-8 pt-6 border-t border-gray-100 flex flex-wrap items-center justify-center gap-6 sm:gap-10 text-xs sm:text-sm font-medium text-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-black"></span>
                        <span>12.400+ pemasok</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-black"></span>
                        <span>38 kategori</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-black"></span>
                        <span>Data perusahaan terverifikasi</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Pratinjau Lembar Komparasi -->
    <section class="py-16 bg-surface">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-black text-white flex items-center justify-center">
                            <x-heroicon-o-scale class="w-5 h-5" />
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Pratinjau Lembar Komparasi</h2>
                            <p class="text-xs text-gray-500">Spesifikasi teknis & kepatuhan pabrik</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                        3 Pemasok Terpilih
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 text-gray-500 uppercase tracking-wider text-xs border-b border-gray-100">
                                <th class="p-4 sm:p-5 font-semibold">Parameter</th>
                                <th class="p-4 sm:p-5 font-semibold text-gray-900">Atlas Packaging</th>
                                <th class="p-4 sm:p-5 font-semibold text-gray-900">Nusantara Plastics</th>
                                <th class="p-4 sm:p-5 font-semibold text-gray-900">Indo-Tech Solutions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium">
                            <tr>
                                <td class="p-4 sm:p-5 text-gray-500">Kapasitas produksi</td>
                                <td class="p-4 sm:p-5 text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <span>2,5 juta unit/bln</span>
                                        <span class="px-2 py-0.5 text-2xs font-bold bg-black text-white rounded">TERBAIK</span>
                                    </div>
                                </td>
                                <td class="p-4 sm:p-5 text-gray-700">1,2 juta unit/bln</td>
                                <td class="p-4 sm:p-5 text-gray-700">800 ribu unit/bln</td>
                            </tr>
                            <tr>
                                <td class="p-4 sm:p-5 text-gray-500">MOQ (Pesanan Minimal)</td>
                                <td class="p-4 sm:p-5 text-gray-700">10.000 unit</td>
                                <td class="p-4 sm:p-5 text-gray-700">5.000 unit</td>
                                <td class="p-4 sm:p-5 text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <span>1.000 unit</span>
                                        <span class="px-2 py-0.5 text-2xs font-bold bg-black text-white rounded">TERBAIK</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-4 sm:p-5 text-gray-500">Lead time produksi</td>
                                <td class="p-4 sm:p-5 text-gray-700">4-6 minggu</td>
                                <td class="p-4 sm:p-5 text-gray-700">3-4 minggu</td>
                                <td class="p-4 sm:p-5 text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <span>2-3 minggu</span>
                                        <span class="px-2 py-0.5 text-2xs font-bold bg-black text-white rounded">TERBAIK</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-4 sm:p-5 text-gray-500">Kepatuhan ISO 9001:2015</td>
                                <td class="p-4 sm:p-5 text-emerald-700 flex items-center gap-1.5 font-semibold">
                                    <x-heroicon-s-check-circle class="w-4 h-4" /> Tervalidasi aktif
                                </td>
                                <td class="p-4 sm:p-5 text-emerald-700">
                                    <div class="flex items-center gap-1.5 font-semibold">
                                        <x-heroicon-s-check-circle class="w-4 h-4" /> Tervalidasi aktif
                                    </div>
                                </td>
                                <td class="p-4 sm:p-5 text-amber-700">
                                    <div class="flex items-center gap-1.5 font-semibold">
                                        <x-heroicon-s-clock class="w-4 h-4" /> Dalam audit
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="text-center text-xs text-gray-400 mt-4">Bandingkan pemasok berdasarkan metrik yang penting.</p>
        </div>
    </section>

    <!-- 3. Alur Pencarian Terstandarisasi (Cara Kerja) -->
    <section id="cara-kerja" class="py-20 bg-white border-t border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">CARA KERJA</span>
            <h2 class="mt-2 text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
                Alur pencarian yang terstandarisasi
            </h2>

            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                <!-- Step 1 -->
                <div class="p-8 rounded-2xl bg-surface border border-gray-200">
                    <div class="w-10 h-10 rounded-full bg-white border border-gray-200 font-bold flex items-center justify-center text-gray-900 mb-6 shadow-2xs">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Cari</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Saring berdasarkan kategori industri, lokasi kota/negara, dan ketersediaan sertifikasi legal.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="p-8 rounded-2xl bg-surface border border-gray-200">
                    <div class="w-10 h-10 rounded-full bg-white border border-gray-200 font-bold flex items-center justify-center text-gray-900 mb-6 shadow-2xs">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Bandingkan</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Lihat kapasitas, MOQ, lead time, dan kepatuhan secara berdampingan tanpa asimetri informasi.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="p-8 rounded-2xl bg-surface border border-gray-200">
                    <div class="w-10 h-10 rounded-full bg-white border border-gray-200 font-bold flex items-center justify-center text-gray-900 mb-6 shadow-2xs">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Hubungi Langsung</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Hubungi pemasok terverifikasi secara langsung via WhatsApp dan Email resmi tanpa perantara.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Transparansi Data Pabrik -->
    <section class="py-20 bg-surface">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">TRANSPARANSI DATA PABRIK</span>
                    <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                        Data yang bisa Anda andalkan
                    </h2>
                    <p class="mt-4 text-base text-gray-600 leading-relaxed">
                        Kami memvalidasi legalitas entitas bisnis, kapasitas fisik, dan keabsahan sertifikat secara berkala demi menjamin integritas rantai pasok Anda.
                    </p>

                    <ul class="mt-8 space-y-6">
                        <li class="flex items-start gap-3">
                            <x-heroicon-s-check-circle class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Identitas perusahaan terverifikasi</h4>
                                <p class="text-xs text-gray-500 mt-1">Pengecekan NIB, akta pendirian, dan domisili operasional pabrik oleh tim kami.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <x-heroicon-s-check-circle class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Sertifikat dan dokumen di satu tempat</h4>
                                <p class="text-xs text-gray-500 mt-1">Akses langsung ke salinan audit ISO, sertifikasi halal, dan laporan uji lab.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <x-heroicon-s-check-circle class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Kolom data seragam di setiap profil</h4>
                                <p class="text-xs text-gray-500 mt-1">Metrik terstruktur mempermudah audit mandiri tanpa informasi asimetris.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Showcase Preview Card -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between gap-4 pb-6 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-black text-white font-bold flex items-center justify-center text-lg">
                                A
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-gray-900 text-lg">Atlas Packaging</h3>
                                    <span class="inline-flex items-center gap-1 text-2xs bg-black text-white px-2 py-0.5 rounded font-medium">
                                        <x-heroicon-s-check class="w-3 h-3 text-emerald-400" /> Terverifikasi
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">Kawasan Industri MM2100, Cikarang</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 py-6 text-center border-b border-gray-100">
                        <div class="bg-gray-50 p-3 rounded-xl">
                            <span class="text-2xs text-gray-400 uppercase font-semibold">Kapasitas</span>
                            <p class="font-bold text-sm text-gray-900 mt-1">2,5 Jt / bln</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl">
                            <span class="text-2xs text-gray-400 uppercase font-semibold">MOQ</span>
                            <p class="font-bold text-sm text-gray-900 mt-1">10.000 Unit</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl">
                            <span class="text-2xs text-gray-400 uppercase font-semibold">Lead Time</span>
                            <p class="font-bold text-sm text-gray-900 mt-1">4-6 Minggu</p>
                        </div>
                    </div>

                    <div class="pt-6">
                        <span class="text-2xs font-bold text-gray-400 uppercase tracking-wider">SERTIFIKASI TERVALIDASI</span>
                        <div class="flex flex-wrap gap-2 mt-3 text-xs">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 text-gray-800 font-medium">
                                <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-emerald-600" /> ISO 9001:2015
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 text-gray-800 font-medium">
                                <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-emerald-600" /> ISO 14001
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 text-gray-800 font-medium">
                                <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-emerald-600" /> HACCP
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Taksonomi Industri (Kategori) -->
    <section class="py-20 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-10 flex-wrap gap-4">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">TAKSONOMI INDUSTRI</span>
                    <h2 class="mt-2 text-3xl font-extrabold text-gray-900 tracking-tight">Jelajahi berdasarkan kategori</h2>
                </div>
                <a href="{{ route('discover.index') }}" class="text-sm font-semibold text-gray-900 hover:text-black flex items-center gap-1 group">
                    Lihat semua sektor <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
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

                @if(isset($categories) && $categories->isNotEmpty())
                    @foreach($categories as $cat)
                        <a href="{{ route('discover.index', ['category' => $cat->name]) }}"
                           class="p-5 rounded-2xl bg-surface hover:bg-gray-100 border border-gray-200 transition group flex flex-col justify-between">
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-800 mb-4 shadow-2xs group-hover:scale-105 transition-transform">
                                <x-dynamic-component :component="'heroicon-o-' . ($categoryIcons[$cat->name] ?? 'building-office-2')" class="w-5 h-5 text-gray-700" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-base group-hover:text-black">{{ $cat->name }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $cat->suppliers_count }} pemasok</p>
                            </div>
                        </a>
                    @endforeach
                @else
                    @foreach([
                        ['name' => 'Packaging Manufacturer', 'icon' => 'archive-box'],
                        ['name' => 'Distributor & Converter', 'icon' => 'truck'],
                        ['name' => 'Contract Manufacturer', 'icon' => 'cog-6-tooth'],
                        ['name' => 'Raw Material Supplier', 'icon' => 'beaker'],
                    ] as $cat)
                        <a href="{{ route('discover.index', ['category' => $cat['name']]) }}"
                           class="p-5 rounded-2xl bg-surface hover:bg-gray-100 border border-gray-200 transition group flex flex-col justify-between">
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-800 mb-4 shadow-2xs group-hover:scale-105 transition-transform">
                                <x-dynamic-component :component="'heroicon-o-' . $cat['icon']" class="w-5 h-5 text-gray-700" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-base group-hover:text-black">{{ $cat['name'] }}</h3>
                                <p class="text-xs text-gray-500 mt-1">Lihat pemasok</p>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- 6. Tingkat Akses (Freemium Pricing) -->
    <section id="paket" class="py-20 bg-surface">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">TINGKAT AKSES</span>
            <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                Mulai gratis. Upgrade saat Anda butuh data lengkap.
            </h2>

            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-8 text-left">
                <!-- Free Plan -->
                <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-xs flex flex-col justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Gratis</h3>
                        <p class="text-xs text-gray-500 mt-1">Untuk eksplorasi awal dan kontak langsung pemasok.</p>

                        <ul class="mt-8 space-y-3.5 text-xs sm:text-sm">
                            <li class="flex items-center gap-2.5 text-gray-800">
                                <x-heroicon-s-check class="w-4 h-4 text-black shrink-0" /> Cari semua pemasok
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-800">
                                <x-heroicon-s-check class="w-4 h-4 text-black shrink-0" /> Profil perusahaan dasar
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-800">
                                <x-heroicon-s-check class="w-4 h-4 text-black shrink-0" /> Hubungi pemasok
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-800">
                                <x-heroicon-s-check class="w-4 h-4 text-black shrink-0" /> Bandingkan hingga 4 pemasok (info dasar)
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-400 line-through">
                                <span class="w-4 text-center">—</span> Metrik operasional (Tidak termasuk)
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-400 line-through">
                                <span class="w-4 text-center">—</span> Semua sertifikasi (Tidak termasuk)
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-400 line-through">
                                <span class="w-4 text-center">—</span> Pustaka dokumen lengkap (Tidak termasuk)
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-400 line-through">
                                <span class="w-4 text-center">—</span> Ekspor PDF perbandingan (Tidak termasuk)
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('register') }}" class="mt-8 block text-center py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold text-sm rounded-xl transition">
                        Mulai gratis
                    </a>
                </div>

                <!-- Premium Plan -->
                <div class="bg-white p-8 rounded-2xl border-2 border-black shadow-lg relative flex flex-col justify-between">
                    <span class="absolute -top-3.5 right-6 px-3 py-1 bg-black text-white text-2xs uppercase tracking-wider font-bold rounded-full">
                        Direkomendasikan
                    </span>

                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Premium</h3>
                        <p class="text-xs text-gray-500 mt-1">Visibilitas menyeluruh untuk tim pengadaan profesional.</p>

                        <ul class="mt-8 space-y-3.5 text-xs sm:text-sm">
                            <li class="flex items-center gap-2.5 text-gray-900 font-medium">
                                <x-heroicon-s-check class="w-4 h-4 text-emerald-600 shrink-0" /> Cari semua pemasok
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-900 font-medium">
                                <x-heroicon-s-check class="w-4 h-4 text-emerald-600 shrink-0" /> Profil perusahaan dasar
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-900 font-medium">
                                <x-heroicon-s-check class="w-4 h-4 text-emerald-600 shrink-0" /> Hubungi pemasok
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-900 font-medium">
                                <x-heroicon-s-check class="w-4 h-4 text-emerald-600 shrink-0" /> Bandingkan hingga 4 pemasok (info dasar)
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-900 font-medium">
                                <x-heroicon-s-check class="w-4 h-4 text-emerald-600 shrink-0" /> Metrik operasional
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-900 font-medium">
                                <x-heroicon-s-check class="w-4 h-4 text-emerald-600 shrink-0" /> Semua sertifikasi
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-900 font-medium">
                                <x-heroicon-s-check class="w-4 h-4 text-emerald-600 shrink-0" /> Pustaka dokumen lengkap
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-900 font-medium">
                                <x-heroicon-s-check class="w-4 h-4 text-emerald-600 shrink-0" /> Ekspor PDF perbandingan
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('upgrade') }}" class="mt-8 block text-center py-3 px-4 bg-black hover:bg-gray-800 text-white font-semibold text-sm rounded-xl transition shadow-md">
                        Upgrade ke Premium
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. FAQ Accordion -->
    <section class="py-20 bg-white border-t border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">TANYA JAWAB</span>
                <h2 class="mt-2 text-3xl font-extrabold text-gray-900 tracking-tight">Pertanyaan yang sering diajukan</h2>
            </div>

            <div class="space-y-4" x-data="{ activeAccordion: 1 }">
                @php
                    $faqs = [
                        [
                            'id' => 1,
                            'q' => 'Apakah Namabiz gratis digunakan?',
                            'a' => 'Ya, Anda dapat menelusuri katalog pemasok, melihat profil informasi dasar, dan menghubungi vendor secara langsung tanpa biaya operasional.'
                        ],
                        [
                            'id' => 2,
                            'q' => 'Bagaimana pemasok diverifikasi?',
                            'a' => 'Tim kami memvalidasi legalitas badan usaha (NIB, SIUP), melakukan audit sampling terhadap fasilitas pabrik, dan memverifikasi keabsahan dokumen sertifikasi ke badan penerbit resmi.'
                        ],
                        [
                            'id' => 3,
                            'q' => 'Apa yang saya dapatkan dengan Premium?',
                            'a' => 'Akses penuh ke seluruh metrik operasional (kapasitas terpasang, MOQ, lead time produksi), pustaka dokumen lengkap (laporan audit pabrik, sertifikasi mutu), dan ekspor perbandingan ke PDF.'
                        ],
                        [
                            'id' => 4,
                            'q' => 'Bisakah saya berhenti berlangganan kapan saja?',
                            'a' => 'Tentu saja. Anda dapat membatalkan atau mengubah paket langganan kapan saja dari halaman pengaturan akun Anda tanpa biaya tersembunyi.'
                        ],
                        [
                            'id' => 5,
                            'q' => 'Apakah data perusahaan saya terjaga privasinya?',
                            'a' => 'Privasi Anda adalah prioritas kami. Data pencarian, preferensi, dan perbandingan Anda terlindungi dan tidak akan dibagikan kepada pihak ketiga tanpa izin eksplisit.'
                        ],
                    ];
                @endphp

                @foreach($faqs as $faq)
                    <div class="border border-gray-200 rounded-xl overflow-hidden transition">
                        <button type="button"
                                @click="activeAccordion = (activeAccordion === {{ $faq['id'] }} ? null : {{ $faq['id'] }})"
                                class="w-full py-4 px-6 text-left flex items-center justify-between text-sm sm:text-base font-semibold text-gray-900 bg-white hover:bg-gray-50 transition">
                            <span>{{ $faq['q'] }}</span>
                            <svg class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                 :class="{ 'rotate-180': activeAccordion === {{ $faq['id'] }} }"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeAccordion === {{ $faq['id'] }}" x-collapse x-cloak
                             class="px-6 pb-5 pt-1 text-xs sm:text-sm text-gray-600 bg-white leading-relaxed">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 8. Bottom CTA Banner -->
    <section class="py-16 bg-surface">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-100 rounded-3xl p-8 sm:p-12 flex flex-col md:flex-row items-center justify-between gap-8 border border-gray-200">
                <div class="max-w-xl text-center md:text-left">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                        Siap menemukan pemasok berikutnya?
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Bergabunglah dengan ribuan pelaku industri yang mengoptimalkan pengadaan B2B.
                    </p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-3 shrink-0">
                    <a href="{{ route('register') }}" class="px-6 py-3 bg-black hover:bg-gray-800 text-white font-semibold text-sm rounded-xl transition shadow-sm">
                        Buat akun gratis
                    </a>
                    <a href="{{ route('discover.index') }}" class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-800 border border-gray-200 font-semibold text-sm rounded-xl transition">
                        Jelajahi pemasok
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
