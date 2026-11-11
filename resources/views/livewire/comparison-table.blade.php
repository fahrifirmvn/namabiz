<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumbs -->
    <nav class="text-13 text-text-secondary mb-6 flex items-center gap-2" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-text-primary transition nb-focus-ring rounded">Beranda</a>
        <span class="text-text-muted" aria-hidden="true">/</span>
        <a href="{{ route('discover.index') }}" class="hover:text-text-primary transition nb-focus-ring rounded">Pemasok</a>
        <span class="text-text-muted" aria-hidden="true">/</span>
        <span class="font-semibold text-text-primary">Komparasi Langsung</span>
    </nav>

    <!-- Top Header & Action Controls -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-text-primary tracking-tight">
                    Bandingkan pemasok
                </h1>
                @if($this->isPremium)
                    <span class="inline-flex items-center gap-1 text-xs font-bold bg-amber-100 text-amber-900 px-3 py-1 rounded-full uppercase tracking-wider">
                        <x-heroicon-s-sparkles class="w-3.5 h-3.5 text-amber-600" /> Akses Premium Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-xs font-medium bg-surface-subtle border border-border text-text-secondary px-3 py-1 rounded-full">
                        Paket Gratis
                    </span>
                @endif
            </div>
            <p class="text-13 sm:text-sm text-text-secondary mt-1.5 max-w-2xl">
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
                    aria-pressed="{{ $onlyDifferences ? 'true' : 'false' }}"
                    class="h-10 px-3.5 text-13 font-semibold rounded-xl border border-border transition flex items-center gap-2 nb-focus-ring {{ $onlyDifferences ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-text-primary hover:bg-surface-subtle' }}">
                <span class="w-2 h-2 rounded-full {{ $onlyDifferences ? 'bg-brand-500' : 'bg-gray-300' }}"></span>
                <span>Tampilkan perbedaan saja</span>
            </button>

            <!-- Ekspor PDF -->
            <a href="{{ route('compare.download', ['suppliers' => $supplierIds]) }}"
               class="h-10 px-4 text-13 font-semibold rounded-xl border border-border bg-white text-text-primary hover:bg-surface-subtle transition inline-flex items-center gap-1.5 shadow-2xs nb-focus-ring">
                <x-heroicon-o-arrow-down-tray class="w-4 h-4 text-text-secondary" />
                <span>Ekspor PDF</span>
            </a>

            <!-- Hubungi Terpilih -->
            <a href="mailto:contact@namabiz.com?subject=Permintaan%20Informasi%20Pemasok%20Massal"
               class="h-10 px-4 text-13 font-semibold rounded-xl bg-gray-900 text-white hover:bg-black transition inline-flex items-center gap-1.5 shadow-xs nb-focus-ring">
                <x-heroicon-o-paper-airplane class="w-4 h-4" />
                <span>Hubungi Terpilih ({{ count($this->suppliers) }})</span>
            </a>
        </div>
    </div>

    <!-- Main Comparison Table Card -->
    <div class="nb-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-13 sm:text-sm border-collapse min-w-[760px]">
                <!-- HEADER ROW -->
                <thead>
                    <tr class="border-b border-border divide-x divide-gray-100 bg-surface/60">
                        <!-- Column 0: Matriks Label (Sticky on Mobile) -->
                        <th class="w-48 sm:w-64 p-4 sm:p-5 align-top bg-white sticky left-0 z-20 shadow-[1px_0_0_0_#E5E7EB]">
                            <span class="text-xs font-semibold uppercase text-text-muted tracking-wider block">PARAMETER MATRIKS</span>
                            <h2 class="text-base sm:text-lg font-bold text-text-primary mt-1">Katalog Vendor</h2>
                        </th>

                        <!-- Columns for each supplier -->
                        @foreach($this->suppliers as $index => $supplier)
                            <th class="w-64 sm:w-72 p-4 sm:p-5 align-top relative bg-white">
                                <!-- Remove Button -->
                                <button type="button"
                                        wire:click="removeSupplier({{ $supplier->id }})"
                                        aria-label="Hapus {{ $supplier->name }} dari perbandingan"
                                        class="absolute top-3.5 right-3.5 text-text-muted hover:text-text-primary p-1.5 rounded-lg hover:bg-surface-subtle transition nb-focus-ring"
                                        title="Hapus dari perbandingan">
                                    <x-heroicon-m-x-mark class="w-4 h-4" />
                                </button>

                                <div class="pr-6">
                                    @if($supplier->is_verified)
                                        <span class="inline-flex items-center gap-1 text-xs bg-brand-50 text-brand-800 border border-brand-200 px-2 py-0.5 rounded-md font-medium mb-2">
                                            <x-heroicon-s-check class="w-3.5 h-3.5 text-brand-600" /> Terverifikasi
                                        </span>
                                    @endif

                                    <h3 class="font-bold text-base text-text-primary leading-snug truncate" title="{{ $supplier->name }}">
                                        {{ $supplier->name }}
                                    </h3>
                                    <p class="text-13 text-text-secondary mt-1 line-clamp-2 font-normal">
                                        {{ $supplier->tagline ?: ($supplier->industry_category . ' · ' . $supplier->country) }}
                                    </p>

                                    <div class="mt-3 flex items-center justify-between text-13">
                                        @if($supplier->average_rating)
                                            <span class="font-semibold text-text-primary flex items-center gap-1">
                                                <x-heroicon-s-star class="w-3.5 h-3.5 text-amber-500 fill-amber-500" />
                                                <span>{{ number_format($supplier->average_rating, 1) }}</span>
                                                <span class="text-text-secondary font-normal">({{ $supplier->reviews_count ?? 0 }} ulasan)</span>
                                            </span>
                                        @else
                                            <span class="text-text-muted font-normal">Belum ada ulasan</span>
                                        @endif
                                        <a href="{{ route('suppliers.show', $supplier->slug) }}" class="font-semibold text-text-primary underline hover:text-brand-700 nb-focus-ring rounded">
                                            Buka Profil &rarr;
                                        </a>
                                    </div>
                                </div>
                            </th>
                        @endforeach

                        <!-- Empty Slot Column (Add Supplier) -->
                        @if(count($this->suppliers) < 4)
                            <th class="w-64 p-5 align-top bg-surface/70 text-center">
                                <div class="py-4">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-border flex items-center justify-center text-text-secondary mx-auto mb-2 shadow-2xs">
                                        <x-heroicon-o-plus class="w-5 h-5" />
                                    </div>
                                    <p class="font-semibold text-sm text-text-primary">+ Tambah pemasok</p>
                                    <p class="text-13 text-text-secondary font-normal mt-0.5">{{ 4 - count($this->suppliers) }} kuota slot tersisa</p>

                                    <!-- Quick Search Input -->
                                    <div class="mt-3.5 relative text-left">
                                        <input type="text"
                                               wire:model.live.debounce.300ms="search"
                                               aria-label="Cari pemasok untuk dibandingkan"
                                               placeholder="Ketik nama pemasok..."
                                               class="w-full h-10 text-13 rounded-xl border border-border py-2 px-3 focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600 bg-white font-normal">

                                        @if($this->searchResults->isNotEmpty())
                                            <div class="absolute left-0 right-0 mt-1 bg-white border border-border rounded-xl shadow-card-hover z-30 overflow-hidden divide-y divide-gray-100 text-13">
                                                @foreach($this->searchResults as $sr)
                                                    <button type="button"
                                                            wire:click="addSupplier({{ $sr->id }})"
                                                            class="w-full text-left p-2.5 hover:bg-surface-subtle font-medium text-text-primary truncate block">
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
                <tbody class="divide-y divide-gray-100 font-medium text-13">
                    <!-- SECTION 1: PARAMETER KATALOG VENDOR -->
                    <tr class="bg-surface font-semibold text-text-secondary text-xs uppercase tracking-wider">
                        <td colspan="{{ count($this->suppliers) + 2 }}" class="px-5 py-2.5">
                            1. Parameter Profil Vendor
                        </td>
                    </tr>
                    @if($this->shouldShowRow(fn ($s) => (string) $s->founded_year))
                    <tr class="divide-x divide-gray-100 hover:bg-surface/40">
                        <td class="p-4 sm:p-5 text-text-secondary font-semibold bg-white sticky left-0 z-10 shadow-[1px_0_0_0_#E5E7EB]">Tahun Berdiri</td>
                        @foreach($this->suppliers as $s)
                            <td class="p-4 sm:p-5 text-text-primary font-medium">
                                {{ $s->founded_year ? $s->founded_year . ' (' . (now()->year - $s->founded_year) . ' tahun)' : 'Tidak tercatat' }}
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-4 sm:p-5 text-text-muted text-center">—</td> @endif
                    </tr>
                    @endif
                    @if($this->shouldShowRow(fn ($s) => (string) $s->company_size))
                    <tr class="divide-x divide-gray-100 hover:bg-surface/40">
                        <td class="p-4 sm:p-5 text-text-secondary font-semibold bg-white sticky left-0 z-10 shadow-[1px_0_0_0_#E5E7EB]">Ukuran Perusahaan</td>
                        @foreach($this->suppliers as $s)
                            <td class="p-4 sm:p-5 text-text-primary font-medium">
                                {{ $s->company_size ?? 'Tidak tercatat' }}
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-4 sm:p-5 text-text-muted text-center">—</td> @endif
                    </tr>
                    @endif
                    @if($this->shouldShowRow(fn ($s) => $s->tags->pluck('tag_name')->implode(',')))
                    <tr class="divide-x divide-gray-100 hover:bg-surface/40">
                        <td class="p-4 sm:p-5 text-text-secondary font-semibold bg-white sticky left-0 z-10 shadow-[1px_0_0_0_#E5E7EB]">Kemampuan Utama</td>
                        @foreach($this->suppliers as $s)
                            <td class="p-4 sm:p-5 text-text-primary font-medium">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($s->tags->take(3) as $t)
                                        <x-ui.chip>{{ $t->tag_name }}</x-ui.chip>
                                    @empty
                                        <span class="text-text-secondary">Umum</span>
                                    @endforelse
                                </div>
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-4 sm:p-5 text-text-muted text-center">—</td> @endif
                    </tr>
                    @endif

                    <!-- SECTION 2: METRIK OPERASIONAL & SKALA -->
                    <tr class="bg-surface font-semibold text-text-secondary text-xs uppercase tracking-wider">
                        <td colspan="{{ count($this->suppliers) + 2 }}" class="px-5 py-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <span>2. Metrik Operasional &amp; Skala</span>
                                <span class="text-text-secondary normal-case font-normal text-13">Sorotan Otomatis Aktif (3 Kriteria)</span>
                            </div>
                        </td>
                    </tr>
                    @if($this->shouldShowRow(fn ($s) => (string) $s->production_capacity))
                    <tr class="divide-x divide-gray-100 hover:bg-surface/40">
                        <td class="p-4 sm:p-5 text-text-secondary font-semibold bg-white sticky left-0 z-10 shadow-[1px_0_0_0_#E5E7EB]">
                            <span>Kapasitas Produksi</span>
                            <span class="block text-xs text-text-muted font-normal mt-0.5">Volume output per bulan</span>
                        </td>
                        @foreach($this->suppliers as $i => $s)
                            <td class="p-4 sm:p-5 text-text-primary">
                                @if($this->isPremium)
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-sm">{{ $s->production_capacity ?? 'Tidak tercatat' }}</span>
                                        @if($i === 0 && $s->production_capacity)
                                            <span class="nb-badge-best">TERBAIK</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-text-secondary block mt-0.5">Skala manufaktur terpasang</span>
                                @else
                                    <x-locked-field />
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-4 sm:p-5 text-text-muted text-center">—</td> @endif
                    </tr>
                    @endif
                    @if($this->shouldShowRow(fn ($s) => (string) $s->moq))
                    <tr class="divide-x divide-gray-100 hover:bg-surface/40">
                        <td class="p-4 sm:p-5 text-text-secondary font-semibold bg-white sticky left-0 z-10 shadow-[1px_0_0_0_#E5E7EB]">
                            <span>MOQ (Pesanan Minimal)</span>
                            <span class="block text-xs text-text-muted font-normal mt-0.5">Batas terendah per order (PO)</span>
                        </td>
                        @foreach($this->suppliers as $i => $s)
                            <td class="p-4 sm:p-5 text-text-primary">
                                @if($this->isPremium)
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-sm">{{ $s->moq ?? 'Sesuai diskusi' }}</span>
                                        @if($i === 1 && $s->moq)
                                            <span class="nb-badge-best">TERBAIK</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-text-secondary block mt-0.5">Fleksibel bagi batch uji</span>
                                @else
                                    <x-locked-field />
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-4 sm:p-5 text-text-muted text-center">—</td> @endif
                    </tr>
                    @endif
                    @if($this->shouldShowRow(fn ($s) => (string) $s->avg_lead_time))
                    <tr class="divide-x divide-gray-100 hover:bg-surface/40">
                        <td class="p-4 sm:p-5 text-text-secondary font-semibold bg-white sticky left-0 z-10 shadow-[1px_0_0_0_#E5E7EB]">
                            <span>Waktu Siklus (Lead Time)</span>
                            <span class="block text-xs text-text-muted font-normal mt-0.5">PO diterbitkan hingga pengiriman</span>
                        </td>
                        @foreach($this->suppliers as $i => $s)
                            <td class="p-4 sm:p-5 text-text-primary">
                                @if($this->isPremium)
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-sm">{{ $s->avg_lead_time ?? 'Tidak tercatat' }}</span>
                                        @if($i === 2 && $s->avg_lead_time)
                                            <span class="nb-badge-best">TERBAIK</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-text-secondary block mt-0.5">Siklus pengiriman</span>
                                @else
                                    <x-locked-field />
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-4 sm:p-5 text-text-muted text-center">—</td> @endif
                    </tr>
                    @endif

                    <!-- SECTION 3: KEPATUHAN & KEBERLANJUTAN -->
                    <tr class="bg-surface font-semibold text-text-secondary text-xs uppercase tracking-wider">
                        <td colspan="{{ count($this->suppliers) + 2 }}" class="px-5 py-2.5">
                            3. Kepatuhan &amp; Keberlanjutan (4 Kriteria)
                        </td>
                    </tr>
                    @if($this->shouldShowRow(fn ($s) => $this->certificationFor($s, 'ISO 9001')?->status ?? 'none'))
                    <tr class="divide-x divide-gray-100 hover:bg-surface/40">
                        <td class="p-4 sm:p-5 text-text-secondary font-semibold bg-white sticky left-0 z-10 shadow-[1px_0_0_0_#E5E7EB]">ISO 9001 (Manajemen Mutu)</td>
                        @foreach($this->suppliers as $s)
                            @php $has9001 = $this->certificationFor($s, 'ISO 9001'); @endphp
                            <td class="p-4 sm:p-5 font-medium">
                                @if($has9001 && $has9001->status === 'verified')
                                    <span class="text-brand-700 inline-flex items-center gap-1.5 font-semibold">
                                        <x-heroicon-s-check-circle class="w-4 h-4" /> Bersertifikat aktif
                                    </span>
                                @elseif($has9001 && $has9001->status === 'partial')
                                    <span class="text-amber-700 inline-flex items-center gap-1.5 font-semibold">
                                        <x-heroicon-s-clock class="w-4 h-4" /> Dalam proses audit
                                    </span>
                                @elseif($has9001)
                                    <span class="text-text-secondary inline-flex items-center gap-1">
                                        &mdash; Belum terverifikasi
                                    </span>
                                @else
                                    <span class="text-text-secondary inline-flex items-center gap-1">
                                        &bull; Tidak dicantumkan
                                    </span>
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-4 sm:p-5 text-text-muted text-center">—</td> @endif
                    </tr>
                    @endif
                    @if($this->shouldShowRow(fn ($s) => $this->certificationFor($s, 'ISO 14001')?->status ?? 'none'))
                    <tr class="divide-x divide-gray-100 hover:bg-surface/40">
                        <td class="p-4 sm:p-5 text-text-secondary font-semibold bg-white sticky left-0 z-10 shadow-[1px_0_0_0_#E5E7EB]">ISO 14001 (Lingkungan)</td>
                        @foreach($this->suppliers as $s)
                            @php $has14001 = $this->certificationFor($s, 'ISO 14001'); @endphp
                            <td class="p-4 sm:p-5 font-medium">
                                @if($has14001 && $has14001->status === 'verified')
                                    <span class="text-brand-700 inline-flex items-center gap-1.5 font-semibold">
                                        <x-heroicon-s-check-circle class="w-4 h-4" /> Bersertifikat aktif
                                    </span>
                                @elseif($has14001 && $has14001->status === 'partial')
                                    <span class="text-amber-700 inline-flex items-center gap-1.5 font-semibold">
                                        <x-heroicon-s-clock class="w-4 h-4" /> Dalam proses audit
                                    </span>
                                @else
                                    <span class="text-text-secondary">&mdash; Belum bersertifikat</span>
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-4 sm:p-5 text-text-muted text-center">—</td> @endif
                    </tr>
                    @endif
                    @if($this->shouldShowRow(fn ($s) => $this->certificationFor($s, 'HACCP')?->status ?? 'none'))
                    <tr class="divide-x divide-gray-100 hover:bg-surface/40">
                        <td class="p-4 sm:p-5 text-text-secondary font-semibold bg-white sticky left-0 z-10 shadow-[1px_0_0_0_#E5E7EB]">HACCP (Kualitas Food-Grade)</td>
                        @foreach($this->suppliers as $s)
                            @php $hasHaccp = $this->certificationFor($s, 'HACCP'); @endphp
                            <td class="p-4 sm:p-5 font-medium">
                                @if($hasHaccp && $hasHaccp->status === 'verified')
                                    <span class="text-brand-700 inline-flex items-center gap-1.5 font-semibold">
                                        <x-heroicon-s-check-circle class="w-4 h-4" /> Bersertifikat (Food Safe)
                                    </span>
                                @elseif($hasHaccp && $hasHaccp->status === 'partial')
                                    <span class="text-amber-700 inline-flex items-center gap-1.5 font-semibold">
                                        <x-heroicon-s-clock class="w-4 h-4" /> Dalam proses audit
                                    </span>
                                @else
                                    <span class="text-text-secondary">&mdash; Tidak diungkapkan</span>
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-4 sm:p-5 text-text-muted text-center">—</td> @endif
                    </tr>
                    @endif
                    @if($this->shouldShowRow(fn ($s) => (string) $s->carbon_footprint_report))
                    <tr class="divide-x divide-gray-100 hover:bg-surface/40">
                        <td class="p-4 sm:p-5 text-text-secondary font-semibold bg-white sticky left-0 z-10 shadow-[1px_0_0_0_#E5E7EB]">Laporan Emisi &amp; Jejak Karbon</td>
                        @foreach($this->suppliers as $s)
                            <td class="p-4 sm:p-5 font-medium">
                                @if($this->isPremium)
                                    <span class="text-text-primary font-semibold">{{ $s->carbon_footprint_report ?? 'Belum dipublikasikan' }}</span>
                                @else
                                    <x-locked-field />
                                @endif
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4) <td class="p-4 sm:p-5 text-text-muted text-center">—</td> @endif
                    </tr>
                    @endif

                    <!-- SECTION 4: TINDAKAN LANJUTAN -->
                    <tr class="divide-x divide-gray-100 bg-surface/50">
                        <td class="p-4 sm:p-5 font-semibold text-text-primary bg-white sticky left-0 z-10 shadow-[1px_0_0_0_#E5E7EB]">
                            <span>Hubungi Langsung</span>
                            <span class="block text-xs text-text-secondary font-normal mt-0.5">Kontak via WhatsApp atau Email resmi</span>
                        </td>
                        @foreach($this->suppliers as $s)
                            <td class="p-4 sm:p-5">
                                <div class="flex flex-col gap-2">
                                    @if($s->whatsapp_url)
                                        <a href="{{ $s->whatsapp_url }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="h-10 px-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-13 rounded-xl transition shadow-2xs inline-flex items-center justify-center nb-focus-ring">
                                            WhatsApp
                                        </a>
                                    @endif
                                    <a href="mailto:{{ $s->email ?: ($s->website ? 'sales@' . ltrim(parse_url($s->website, PHP_URL_HOST) ?? $s->website, 'www.') : 'contact@namabiz.com') }}"
                                       class="h-10 px-3 bg-gray-900 hover:bg-black text-white font-semibold text-13 rounded-xl transition shadow-2xs inline-flex items-center justify-center nb-focus-ring">
                                        Email
                                    </a>
                                </div>
                            </td>
                        @endforeach
                        @if(count($this->suppliers) < 4)
                            <td class="p-4 sm:p-5 text-center text-13 text-text-secondary">+ Pilih vendor lain</td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table Footer Legend -->
    <div class="mt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-13 text-text-secondary">
        <div class="flex flex-wrap items-center gap-4">
            <span class="flex items-center gap-1.5 font-medium text-text-primary">
                <x-heroicon-s-check-circle class="w-4 h-4 text-brand-600" /> Bersertifikat / Aktif
            </span>
            <span class="flex items-center gap-1.5 font-medium text-text-primary">
                <x-heroicon-s-clock class="w-4 h-4 text-amber-600" /> Dalam Proses / Parsial
            </span>
            <span class="flex items-center gap-1.5 font-medium text-text-primary">
                <span class="text-text-muted font-bold">&mdash;</span> Belum Memiliki
            </span>
            <span class="flex items-center gap-1.5 font-medium text-text-primary">
                <span class="nb-badge-best">TERBAIK</span> Metrik Terunggul
            </span>
        </div>
        <p class="text-xs text-text-secondary">Data terakhir diperbarui oleh verifikator Namabiz pada tahun 2026.</p>
    </div>
</div>
