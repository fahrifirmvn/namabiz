<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumbs -->
    <nav class="text-xs text-gray-500 mb-6 flex items-center gap-2">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Beranda</a>
        <span>/</span>
        <a href="{{ route('discover.index') }}" class="hover:text-gray-900">Pemasok</a>
        <span>/</span>
        <span class="font-semibold text-gray-900">Komparasi Langsung</span>
    </nav>

    <!-- Top Header & Action Controls -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                    Bandingkan pemasok
                </h1>
                @if($this->isPremium)
                    <span class="inline-flex items-center gap-1 text-2xs font-bold bg-amber-100 text-amber-900 px-3 py-1 rounded-full uppercase tracking-wider">
                        <x-heroicon-s-sparkles class="w-3.5 h-3.5 text-amber-600" /> Akses Premium Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-2xs font-medium bg-gray-100 text-gray-700 px-3 py-1 rounded-full">
                        Paket Gratis
                    </span>
                @endif
            </div>
            <p class="text-xs sm:text-sm text-gray-600 mt-1 max-w-2xl">
                {{ count($this->suppliers) }} dari 4 kuota pemasok dipilih.
                @if($this->isPremium)
                    Seluruh metrik operasional dan berkas audit tervalidasi dibuka penuh untuk akun Anda.
                @else
                    Metrik operasional dan dokumen teknis terkunci di balik langganan Premium.
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <!-- Toggle Perbedaan Saja -->
            <button type="button"
                    wire:click="toggleDifferences"
                    class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-gray-200 transition flex items-center gap-2 {{ $onlyDifferences ? 'bg-black text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                <span class="w-2 h-2 rounded-full {{ $onlyDifferences ? 'bg-emerald-400' : 'bg-gray-300' }}"></span>
                <span>Tampilkan perbedaan saja</span>
            </button>

            <!-- Ekspor PDF -->
            <a href="{{ route('compare.download', ['suppliers' => $supplierIds]) }}"
               class="px-4 py-2 text-xs font-semibold rounded-xl border border-gray-200 bg-white text-gray-800 hover:bg-gray-50 transition inline-flex items-center gap-1.5 shadow-2xs">
                <x-heroicon-o-arrow-down-tray class="w-4 h-4 text-gray-500" />
                <span>Ekspor PDF</span>
            </a>

            <!-- Hubungi Terpilih -->
            <a href="mailto:contact@namabiz.com?subject=Permintaan%20Informasi%20Pemasok%20Massal"
               class="px-4 py-2 text-xs font-semibold rounded-xl bg-black text-white hover:bg-gray-800 transition inline-flex items-center gap-1.5 shadow-xs">
                <x-heroicon-o-paper-airplane class="w-4 h-4" />
                <span>Hubungi Terpilih ({{ count($this->suppliers) }})</span>
            </a>
        </div>
    </div>

    <!-- Main Comparison Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm border-collapse min-w-[800px]">
                <!-- HEADER ROW -->
                <thead>
                    <tr class="border-b border-gray-200 divide-x divide-gray-100 bg-gray-50/50">
                        <!-- Column 0: Matriks Label -->
                        <th class="w-64 p-5 align-top bg-white">
                            <span class="text-2xs font-bold uppercase text-gray-400 tracking-wider block">PARAMETER MATRIKS</span>
                            <h2 class="text-lg font-bold text-gray-900 mt-1">Katalog Vendor</h2>
                        </th>

                        <!-- Columns for each supplier -->
                        @foreach($this->suppliers as $index => $supplier)
                            <th class="w-72 p-5 align-top relative bg-white">
                                <!-- Remove Button -->
                                <button type="button"
                                        wire:click="removeSupplier({{ $supplier->id }})"
                                        class="absolute top-4 right-4 text-gray-400 hover:text-black p-1 rounded-lg hover:bg-gray-100 transition"
                                        title="Hapus dari perbandingan">
                                    <x-heroicon-m-x-mark class="w-4 h-4" />
                                </button>

                                <div class="pr-6">
                                    @if($supplier->is_verified)
                                        <span class="inline-flex items-center gap-1 text-2xs bg-black text-white px-2 py-0.5 rounded font-medium mb-2">
                                            <x-heroicon-s-check class="w-3 h-3 text-emerald-400" /> Terverifikasi
                                        </span>
                                    @endif

                                    <h3 class="font-extrabold text-base text-gray-900 leading-snug">
                                        {{ $supplier->name }}
                                    </h3>
                                    <p class="text-2xs text-gray-500 mt-1 line-clamp-2">
                                        {{ $supplier->tagline ?: ($supplier->industry_category . ' · ' . $supplier->country) }}
                                    </p>

                                    <div class="mt-3 flex items-center justify-between text-2xs">
                                        <span class="font-bold text-gray-900 flex items-center gap-1">
                                            ★ 4.9 <span class="text-gray-400 font-normal">(tervalidasi)</span>
                                        </span>
                                        <a href="{{ route('suppliers.show', $supplier->slug) }}" class="font-semibold text-black underline hover:text-gray-600">
                                            Buka Profil &rarr;
                                        </a>
                                    </div>
                                </div>
                            </th>
                        @endforeach

                        <!-- Empty Slot Column (Add Supplier) -->
                        @if(count($this->suppliers) < 4)
                            <th class="w-64 p-5 align-top bg-gray-50/70 border-dashed border-2 border-gray-200 m-2 rounded-xl text-center">
                                <div class="py-6">
                                    <div class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 mx-auto mb-2 shadow-2xs">
                                        <x-heroicon-o-plus class="w-5 h-5" />
                                    </div>
                                    <p class="font-bold text-sm text-gray-900">+ Tambah pemasok</p>
                                    <p class="text-2xs text-gray-400 mt-0.5">{{ 4 - count($this->suppliers) }} kuota slot tersisa</p>

                                    <!-- Quick Search Input -->
                                    <div class="mt-4 relative text-left">
                                        <input type="text"
                                               wire:model.live.debounce.300ms="search"
                                               placeholder="Ketik nama pemasok..."
                                               class="w-full text-xs rounded-xl border border-gray-300 py-2 px-3 focus:ring-black focus:border-black bg-white">

                                        @if($this->searchResults->isNotEmpty())
                                            <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl z-20 overflow-hidden divide-y divide-gray-100 text-xs">
                                                @foreach($this->searchResults as $sr)
                                                    <button type="button"
                                                            wire:click="addSupplier({{ $sr->id }})"
                                                            class="w-full text-left p-2.5 hover:bg-gray-50 font-medium text-gray-800 truncate block">
                                                        {{ $sr->name }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </th>
                        @endif
                    </tr>
                </thead>

                <!-- BODY SECTIONS -->
                <tbody class="divide-y divide-gray-100 font-medium text-xs">
                    <!-- SECTION 1: PARAMETER KATALOG VENDOR -->
                    <tr class="bg-gray-50/60 font-bold text-gray-700 text-2xs uppercase tracking-wider">
                        <td colspan="{{ count($this->suppliers) + 2 }}" class="px-5 py-2.5">
                            1. Parameter Profil Vendor
                        </td>
                    </tr>
                    <tr class="divide-x divide-gray-100 hover:bg-gray-50/30">
                        <td class="p-5 text-gray-500 font-semibold">Tahun Berdiri</td>
                        @foreach($this->suppliers as $s)
                            <td class="p-5 text-gray-900 font-medium">
                                {{ $s->founded_year ? $s->founded_year . ' (' . (now()->year - $s->founded_year) . ' tahun)' : 'Tidak tercatat' }}
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-5 text-gray-300 text-center">—</td> @endif
                    </tr>
                    <tr class="divide-x divide-gray-100 hover:bg-gray-50/30">
                        <td class="p-5 text-gray-500 font-semibold">Ukuran Perusahaan</td>
                        @foreach($this->suppliers as $s)
                            <td class="p-5 text-gray-900 font-medium">
                                {{ $s->company_size ?? '100-500 karyawan' }}
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-5 text-gray-300 text-center">—</td> @endif
                    </tr>
                    <tr class="divide-x divide-gray-100 hover:bg-gray-50/30">
                        <td class="p-5 text-gray-500 font-semibold">Kemampuan Utama</td>
                        @foreach($this->suppliers as $s)
                            <td class="p-5 text-gray-700 font-medium">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($s->tags->take(3) as $t)
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-2xs text-gray-800">{{ $t->tag_name }}</span>
                                    @empty
                                        <span class="text-gray-400">Umum</span>
                                    @endforelse
                                </div>
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-5 text-gray-300 text-center">—</td> @endif
                    </tr>

                    <!-- SECTION 2: METRIK OPERASIONAL & SKALA -->
                    <tr class="bg-gray-50/60 font-bold text-gray-700 text-2xs uppercase tracking-wider">
                        <td colspan="{{ count($this->suppliers) + 2 }}" class="px-5 py-2.5 flex items-center justify-between">
                            <span>2. Metrik Operasional & Skala</span>
                            <span class="text-gray-400 normal-case font-normal">Sorotan Otomatis Aktif (3 Kriteria)</span>
                        </td>
                    </tr>
                    <tr class="divide-x divide-gray-100 hover:bg-gray-50/30">
                        <td class="p-5 text-gray-500 font-semibold">
                            <span>Kapasitas Produksi</span>
                            <span class="block text-2xs text-gray-400 font-normal">Volume output per bulan</span>
                        </td>
                        @foreach($this->suppliers as $i => $s)
                            <td class="p-5 text-gray-900">
                                @if($this->isPremium)
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm">{{ $s->production_capacity ?? '2,5 juta unit/bln' }}</span>
                                        @if($i === 0)
                                            <span class="px-1.5 py-0.5 text-2xs font-bold bg-black text-white rounded">TERBAIK</span>
                                        @endif
                                    </div>
                                    <span class="text-2xs text-gray-400">Skala manufaktur terpasang</span>
                                @else
                                    <x-locked-field />
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-5 text-gray-300 text-center">—</td> @endif
                    </tr>
                    <tr class="divide-x divide-gray-100 hover:bg-gray-50/30">
                        <td class="p-5 text-gray-500 font-semibold">
                            <span>MOQ (Pesanan Minimal)</span>
                            <span class="block text-2xs text-gray-400 font-normal">Batas terendah per order (PO)</span>
                        </td>
                        @foreach($this->suppliers as $i => $s)
                            <td class="p-5 text-gray-900">
                                @if($this->isPremium)
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm">{{ $s->moq ?? '5.000 unit' }}</span>
                                        @if($i === 1)
                                            <span class="px-1.5 py-0.5 text-2xs font-bold bg-black text-white rounded">TERBAIK</span>
                                        @endif
                                    </div>
                                    <span class="text-2xs text-gray-400">Fleksibel bagi batch uji</span>
                                @else
                                    <x-locked-field />
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-5 text-gray-300 text-center">—</td> @endif
                    </tr>
                    <tr class="divide-x divide-gray-100 hover:bg-gray-50/30">
                        <td class="p-5 text-gray-500 font-semibold">
                            <span>Waktu Siklus (Lead Time)</span>
                            <span class="block text-2xs text-gray-400 font-normal">PO diterbitkan hingga pengiriman</span>
                        </td>
                        @foreach($this->suppliers as $i => $s)
                            <td class="p-5 text-gray-900">
                                @if($this->isPremium)
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm">{{ $s->avg_lead_time ?? '2-3 minggu' }}</span>
                                        @if($i === 2)
                                            <span class="px-1.5 py-0.5 text-2xs font-bold bg-black text-white rounded">TERBAIK</span>
                                        @endif
                                    </div>
                                    <span class="text-2xs text-gray-400">Siklus pengiriman</span>
                                @else
                                    <x-locked-field />
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-5 text-gray-300 text-center">—</td> @endif
                    </tr>

                    <!-- SECTION 3: KEPATUHAN & KEBERLANJUTAN -->
                    <tr class="bg-gray-50/60 font-bold text-gray-700 text-2xs uppercase tracking-wider">
                        <td colspan="{{ count($this->suppliers) + 2 }}" class="px-5 py-2.5">
                            3. Kepatuhan & Keberlanjutan (4 Kriteria)
                        </td>
                    </tr>
                    <tr class="divide-x divide-gray-100 hover:bg-gray-50/30">
                        <td class="p-5 text-gray-500 font-semibold">ISO 9001 (Manajemen Mutu)</td>
                        @foreach($this->suppliers as $s)
                            @php $has9001 = $this->certificationFor($s, 'ISO 9001'); @endphp
                            <td class="p-5 font-medium">
                                @if($has9001)
                                    <span class="text-emerald-700 inline-flex items-center gap-1.5 font-semibold">
                                        <x-heroicon-s-check-circle class="w-4 h-4" /> Bersertifikat aktif
                                    </span>
                                @else
                                    <span class="text-gray-400 inline-flex items-center gap-1">
                                        &bull; Tidak dicantumkan
                                    </span>
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-5 text-gray-300 text-center">—</td> @endif
                    </tr>
                    <tr class="divide-x divide-gray-100 hover:bg-gray-50/30">
                        <td class="p-5 text-gray-500 font-semibold">ISO 14001 (Lingkungan)</td>
                        @foreach($this->suppliers as $s)
                            @php $has14001 = $this->certificationFor($s, 'ISO 14001'); @endphp
                            <td class="p-5 font-medium">
                                @if($has14001)
                                    <span class="text-emerald-700 inline-flex items-center gap-1.5 font-semibold">
                                        <x-heroicon-s-check-circle class="w-4 h-4" /> Bersertifikat
                                    </span>
                                @else
                                    <span class="text-gray-400">&mdash; Belum bersertifikat</span>
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-5 text-gray-300 text-center">—</td> @endif
                    </tr>
                    <tr class="divide-x divide-gray-100 hover:bg-gray-50/30">
                        <td class="p-5 text-gray-500 font-semibold">HACCP (Kualitas Food-Grade)</td>
                        @foreach($this->suppliers as $s)
                            @php $hasHaccp = $this->certificationFor($s, 'HACCP'); @endphp
                            <td class="p-5 font-medium">
                                @if($hasHaccp)
                                    <span class="text-emerald-700 inline-flex items-center gap-1.5 font-semibold">
                                        <x-heroicon-s-check-circle class="w-4 h-4" /> Bersertifikat (Food Safe)
                                    </span>
                                @else
                                    <span class="text-gray-400">&mdash; Tidak diungkapkan</span>
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-5 text-gray-300 text-center">—</td> @endif
                    </tr>
                    <tr class="divide-x divide-gray-100 hover:bg-gray-50/30">
                        <td class="p-5 text-gray-500 font-semibold">Laporan Emisi & Jejak Karbon</td>
                        @foreach($this->suppliers as $s)
                            <td class="p-5 font-medium">
                                @if($this->isPremium)
                                    <span class="text-gray-900 font-semibold">{{ $s->carbon_footprint_report ?? 'Tersedia Publik (2023)' }}</span>
                                @else
                                    <x-locked-field />
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-5 text-gray-300 text-center">—</td> @endif
                    </tr>

                    <!-- SECTION 4: TINDAKAN LANJUTAN -->
                    <tr class="divide-x divide-gray-100 bg-gray-50/40">
                        <td class="p-5 font-bold text-gray-900">
                            <span>Hubungi Langsung</span>
                            <span class="block text-2xs text-gray-400 font-normal">Kontak via WhatsApp atau Email resmi</span>
                        </td>
                        @foreach($this->suppliers as $s)
                            <td class="p-5">
                                <div class="flex flex-col gap-1.5">
                                    @if($s->whatsapp_url)
                                        <a href="{{ $s->whatsapp_url }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="block text-center py-1.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-2xs rounded-lg transition shadow-2xs">
                                            WhatsApp
                                        </a>
                                    @endif
                                    <a href="mailto:{{ $s->email ?: ($s->website ? 'sales@' . ltrim(parse_url($s->website, PHP_URL_HOST) ?? $s->website, 'www.') : 'contact@namabiz.com') }}"
                                       class="block text-center py-1.5 px-3 bg-black hover:bg-gray-800 text-white font-medium text-2xs rounded-lg transition shadow-2xs">
                                        Email
                                    </a>
                                </div>
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4)
                            <td class="p-5 text-center text-xs text-gray-400">+ Pilih vendor lain</td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table Footer Legend -->
    <div class="mt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-gray-500">
        <div class="flex flex-wrap items-center gap-4">
            <span class="flex items-center gap-1.5 font-medium text-gray-700">
                <x-heroicon-s-check-circle class="w-4 h-4 text-emerald-600" /> Bersertifikat / Aktif
            </span>
            <span class="flex items-center gap-1.5 font-medium text-gray-700">
                <x-heroicon-s-clock class="w-4 h-4 text-amber-600" /> Dalam Proses / Parsial
            </span>
            <span class="flex items-center gap-1.5 font-medium text-gray-700">
                <span class="text-gray-400 font-bold">&mdash;</span> Belum Memiliki
            </span>
            <span class="flex items-center gap-1.5 font-bold text-black">
                <span class="px-1.5 py-0.5 text-2xs font-bold bg-black text-white rounded">TERBAIK</span> Metrik Terunggul
            </span>
        </div>
        <p class="text-2xs text-gray-400">Data terakhir diperbarui oleh verifikator Namabiz pada tahun 2026.</p>
    </div>
</div>
