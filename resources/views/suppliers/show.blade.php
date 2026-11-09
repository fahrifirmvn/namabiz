<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeTab: 'ringkasan' }">
        <!-- Breadcrumbs -->
        <nav class="text-xs text-gray-500 mb-6 flex items-center gap-2">
            <a href="{{ route('discover.index') }}" class="hover:text-gray-900">Jelajahi</a>
            <span>/</span>
            <a href="{{ route('discover.index', ['category' => $supplier->industry_category]) }}" class="hover:text-gray-900">{{ $supplier->industry_category }}</a>
            <span>/</span>
            <span class="font-semibold text-gray-900 truncate max-w-xs">{{ $supplier->name }}</span>
        </nav>

        <!-- Top Profile Header Card -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <!-- Left: Logo & Info -->
                <div class="flex items-start sm:items-center gap-4 sm:gap-5 min-w-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-black text-white font-extrabold flex items-center justify-center shrink-0 text-2xl shadow-sm">
                        @if($supplier->logo && !str_contains($supplier->logo, 'placeholder'))
                            <img src="{{ str_starts_with($supplier->logo, 'http') ? $supplier->logo : asset('storage/' . $supplier->logo) }}"
                                 alt="{{ $supplier->name }}"
                                 class="w-full h-full object-cover rounded-2xl" />
                        @else
                            {{ strtoupper(substr($supplier->name, 0, 2)) }}
                        @endif
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                                {{ $supplier->name }}
                            </h1>
                            @if($supplier->is_verified)
                                <span class="inline-flex items-center gap-1 text-xs bg-black text-white px-2.5 py-0.5 rounded font-medium">
                                    <x-heroicon-s-check class="w-3.5 h-3.5 text-emerald-400" /> Terverifikasi
                                </span>
                            @endif
                        </div>

                        <p class="text-xs sm:text-sm text-gray-600 mt-1">
                            {{ $supplier->industry_category }} &bull; {{ $supplier->city ? $supplier->city . ', ' : '' }}{{ $supplier->country }}
                        </p>

                        <!-- Metadata Badges -->
                        <div class="flex flex-wrap items-center gap-3 sm:gap-4 mt-3 text-xs text-gray-500">
                            @if($supplier->founded_year)
                                <span class="inline-flex items-center gap-1.5">
                                    <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" /> Berdiri {{ $supplier->founded_year }}
                                </span>
                            @endif
                            @if($supplier->company_size)
                                <span class="inline-flex items-center gap-1.5">
                                    <x-heroicon-o-user-group class="w-4 h-4 text-gray-400" /> {{ $supplier->company_size }}
                                </span>
                            @endif
                            @if($supplier->website)
                                @php
                                    $websiteUrl = \Illuminate\Support\Str::startsWith($supplier->website, ['http://', 'https://'])
                                        ? $supplier->website
                                        : 'https://' . $supplier->website;
                                @endphp
                                <a href="{{ $websiteUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-gray-700 hover:text-black underline underline-offset-2">
                                    <x-heroicon-o-globe-alt class="w-4 h-4 text-gray-400" /> {{ parse_url($websiteUrl, PHP_URL_HOST) ?? $supplier->website }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Action Buttons -->
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 shrink-0">
                    <a href="mailto:{{ $supplier->website ? 'sales@' . ltrim($supplier->website, 'https://http://') : 'contact@namabiz.com' }}"
                       class="px-5 py-2.5 bg-black hover:bg-gray-800 text-white font-medium text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4" />
                        <span>Hubungi pemasok</span>
                    </a>

                    @auth
                        <form method="POST" action="{{ route('suppliers.save', $supplier) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2.5 border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-medium text-xs rounded-xl transition inline-flex items-center gap-1.5">
                                <x-heroicon-o-bookmark class="w-4 h-4 {{ $isSaved ? 'text-black fill-black' : 'text-gray-500' }}" />
                                <span>{{ $isSaved ? 'Tersimpan' : 'Simpan' }}</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2.5 border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-medium text-xs rounded-xl transition inline-flex items-center gap-1.5">
                            <x-heroicon-o-bookmark class="w-4 h-4 text-gray-500" />
                            <span>Simpan</span>
                        </a>
                    @endauth

                    <a href="{{ route('compare.index', ['suppliers' => [$supplier->id]]) }}" class="px-4 py-2.5 border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-medium text-xs rounded-xl transition inline-flex items-center gap-1.5">
                        <x-heroicon-o-scale class="w-4 h-4 text-gray-500" />
                        <span>Tambah ke perbandingan</span>
                    </a>
                </div>
            </div>

            <!-- Tab Navigation Bar -->
            <div class="mt-8 pt-4 border-t border-gray-100 flex items-center gap-8 text-sm font-medium">
                <button type="button" @click="activeTab = 'ringkasan'"
                        :class="activeTab === 'ringkasan' ? 'border-b-2 border-black font-bold text-gray-900 pb-2' : 'text-gray-500 hover:text-gray-900 pb-2'">
                    Ringkasan
                </button>
                <button type="button" @click="activeTab = 'kemampuan'"
                        :class="activeTab === 'kemampuan' ? 'border-b-2 border-black font-bold text-gray-900 pb-2' : 'text-gray-500 hover:text-gray-900 pb-2'">
                    Kemampuan
                </button>
                <button type="button" @click="activeTab = 'sertifikasi'"
                        :class="activeTab === 'sertifikasi' ? 'border-b-2 border-black font-bold text-gray-900 pb-2' : 'text-gray-500 hover:text-gray-900 pb-2'">
                    Sertifikasi
                </button>
                <button type="button" @click="activeTab = 'dokumen'"
                        :class="activeTab === 'dokumen' ? 'border-b-2 border-black font-bold text-gray-900 pb-2' : 'text-gray-500 hover:text-gray-900 pb-2'">
                    Dokumen
                </button>
            </div>
        </div>

        <!-- 2 Column Layout (Content & Sidebar) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Main Column (2 spans) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- 1. Tentang (About) -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Tentang</h2>
                    <div class="text-sm text-gray-700 leading-relaxed space-y-3">
                        <p>{{ $supplier->about ?: ($supplier->description ?: 'Pemasok terpercaya terdaftar di jaringan Namabiz dengan spesialisasi industri dan sertifikasi resmi.') }}</p>
                    </div>
                </div>

                <!-- 2. Kemampuan Inti -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Kemampuan inti</h2>
                    <div class="flex flex-wrap gap-2">
                        @forelse($supplier->tags as $tag)
                            <span class="px-3.5 py-1.5 rounded-lg bg-gray-100 text-xs font-medium text-gray-800">
                                {{ $tag->tag_name }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-500">Belum ada tag spesialisasi khusus.</span>
                        @endforelse
                    </div>
                </div>

                <!-- 3. Kapabilitas Operasional (Paywalled for non-premium) -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs relative">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">Kapabilitas operasional</h2>
                        @unless(auth()->user()?->isPremium())
                            <span class="inline-flex items-center gap-1 text-2xs font-bold text-gray-700 bg-gray-100 px-2.5 py-1 rounded-full uppercase tracking-wider">
                                <x-heroicon-s-lock-closed class="w-3.5 h-3.5 text-gray-500" /> AKSES PREMIUM
                            </span>
                        @endunless
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <!-- Kapasitas Produksi -->
                        <div class="bg-surface rounded-xl p-4 border border-gray-100">
                            <span class="text-2xs text-gray-400 font-semibold uppercase">Kapasitas produksi</span>
                            @if(auth()->user()?->isPremium())
                                <p class="text-xl font-extrabold text-gray-900 mt-1">{{ $supplier->production_capacity ?? 'N/A' }}</p>
                                <span class="text-2xs text-gray-500">unit / bulan</span>
                            @else
                                <div class="mt-2 filter blur-xs select-none">
                                    <p class="text-xl font-bold text-gray-400">2,5 juta</p>
                                </div>
                                <span class="text-2xs text-amber-700 font-medium">Terkunci</span>
                            @endif
                        </div>

                        <!-- MOQ -->
                        <div class="bg-surface rounded-xl p-4 border border-gray-100">
                            <span class="text-2xs text-gray-400 font-semibold uppercase">MOQ (Pesanan Min)</span>
                            @if(auth()->user()?->isPremium())
                                <p class="text-xl font-extrabold text-gray-900 mt-1">{{ $supplier->moq ?? 'N/A' }}</p>
                                <span class="text-2xs text-gray-500">unit</span>
                            @else
                                <div class="mt-2 filter blur-xs select-none">
                                    <p class="text-xl font-bold text-gray-400">10.000</p>
                                </div>
                                <span class="text-2xs text-amber-700 font-medium">Terkunci</span>
                            @endif
                        </div>

                        <!-- Lead Time -->
                        <div class="bg-surface rounded-xl p-4 border border-gray-100">
                            <span class="text-2xs text-gray-400 font-semibold uppercase">Lead time standar</span>
                            @if(auth()->user()?->isPremium())
                                <p class="text-xl font-extrabold text-gray-900 mt-1">{{ $supplier->avg_lead_time ?? 'N/A' }}</p>
                                <span class="text-2xs text-gray-500">minggu</span>
                            @else
                                <div class="mt-2 filter blur-xs select-none">
                                    <p class="text-xl font-bold text-gray-400">4-6</p>
                                </div>
                                <span class="text-2xs text-amber-700 font-medium">Terkunci</span>
                            @endif
                        </div>

                        <!-- Fasilitas / Jejak Karbon -->
                        <div class="bg-surface rounded-xl p-4 border border-gray-100">
                            <span class="text-2xs text-gray-400 font-semibold uppercase">Audit Jejak Karbon</span>
                            @if(auth()->user()?->isPremium())
                                <p class="text-sm font-bold text-gray-900 mt-1">{{ $supplier->carbon_footprint_report ?? 'Tersedia 2024' }}</p>
                            @else
                                <div class="mt-2 filter blur-xs select-none">
                                    <p class="text-sm font-bold text-gray-400">Tersedia Audit</p>
                                </div>
                                <span class="text-2xs text-amber-700 font-medium">Terkunci</span>
                            @endif
                        </div>
                    </div>

                    @unless(auth()->user()?->isPremium())
                        <div class="mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-2.5 text-xs text-amber-900">
                                <x-heroicon-s-sparkles class="w-4 h-4 text-amber-600 shrink-0" />
                                <span>Buka data operasional penuh untuk verifikasi kapasitas fisik & batas minimum order.</span>
                            </div>
                            <a href="{{ route('upgrade') }}" class="px-3 py-1.5 bg-black text-white rounded-lg text-xs font-semibold hover:bg-gray-800 shrink-0">
                                Upgrade
                            </a>
                        </div>
                    @endunless
                </div>

                <!-- 4. Sertifikasi Terakreditasi -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">Sertifikasi</h2>
                        <span class="text-xs text-gray-500">{{ $supplier->certifications->count() }} terakreditasi</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @forelse($supplier->certifications as $cert)
                            <div class="p-4 rounded-xl bg-surface border border-gray-200">
                                <div class="flex items-center gap-2 mb-2 text-emerald-700">
                                    <x-heroicon-s-check-circle class="w-4 h-4" />
                                    <span class="text-xs font-bold uppercase">{{ $cert->status ?? 'Verified' }}</span>
                                </div>
                                <h4 class="font-bold text-sm text-gray-900">{{ $cert->certification?->name ?? 'ISO Certified' }}</h4>
                                <p class="text-2xs text-gray-500 mt-0.5">{{ $cert->certification?->category ?? 'Standar Mutu Internasional' }}</p>
                            </div>
                        @empty
                            <div class="p-4 rounded-xl bg-surface border border-gray-200">
                                <div class="flex items-center gap-2 mb-2 text-emerald-700">
                                    <x-heroicon-s-check-circle class="w-4 h-4" />
                                    <span class="text-xs font-bold">TERVERIFIKASI</span>
                                </div>
                                <h4 class="font-bold text-sm text-gray-900">ISO 9001:2015</h4>
                                <p class="text-2xs text-gray-500 mt-0.5">Manajemen Mutu Pabrik</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column -->
            <aside class="space-y-6">
                <!-- Status Paket Pengguna Widget -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl {{ auth()->user()?->isPremium() ? 'bg-amber-100 text-amber-900' : 'bg-gray-100 text-gray-700' }} flex items-center justify-center">
                                <x-heroicon-s-sparkles class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-gray-900">
                                    {{ auth()->user()?->isPremium() ? 'Akses Premium Aktif' : 'Paket Gratis' }}
                                </h3>
                                <p class="text-2xs text-gray-500">
                                    {{ auth()->user()?->isPremium() ? 'Visibilitas data tidak terbatas' : 'Metrik operasional terkunci' }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('upgrade') }}" class="text-xs font-semibold text-black hover:underline shrink-0">
                            {{ auth()->user()?->isPremium() ? 'Kelola' : 'Upgrade' }}
                        </a>
                    </div>
                </div>

                <!-- Pustaka Dokumen Widget -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                        <h3 class="font-bold text-sm text-gray-900">Dokumen</h3>
                        <span class="text-2xs font-semibold bg-emerald-50 text-emerald-700 px-2.5 py-0.5 rounded-full">
                            Terverifikasi
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse($supplier->documents as $doc)
                            <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-gray-50 transition border border-gray-100">
                                <div class="flex items-center gap-3 min-w-0">
                                    <x-heroicon-o-document-text class="w-5 h-5 text-gray-400 shrink-0" />
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 truncate">{{ $doc->title }}</p>
                                        <p class="text-2xs text-gray-400 uppercase">{{ $doc->type ?? 'PDF' }} &bull; 1,2 MB</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if(!$doc->is_premium || auth()->user()?->isPremium())
                                        <a href="{{ route('documents.download', $doc) }}" class="p-1.5 text-gray-500 hover:text-black rounded-lg hover:bg-gray-100 transition" title="Unduh berkas">
                                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                                        </a>
                                    @else
                                        <button type="button" @click="$dispatch('open-premium-modal', { title: '{{ $doc->title }}' })" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Terkunci untuk akun Premium">
                                            <x-heroicon-s-lock-closed class="w-4 h-4" />
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400">Belum ada dokumen publik yang diunggah.</p>
                        @endforelse
                    </div>

                    <p class="text-2xs text-gray-400 mt-4 text-center">{{ $supplier->documents->count() }} dokumen tervalidasi tersedia</p>
                </div>

                <!-- Kantor Pusat / Map Widget -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs">
                    <h3 class="font-bold text-sm text-gray-900 mb-4">Kantor pusat & pabrik</h3>
                    <div class="relative w-full h-36 bg-gray-100 rounded-xl overflow-hidden border border-gray-200 flex items-center justify-center">
                        <!-- Stylized Map Placeholder -->
                        <div class="absolute inset-0 bg-emerald-50/40 flex items-center justify-center">
                            <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center shadow-lg animate-bounce">
                                <x-heroicon-s-map-pin class="w-4 h-4" />
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 mt-3 leading-relaxed">
                        {{ $supplier->city ? $supplier->city . ', ' : '' }}{{ $supplier->country }}
                    </p>
                    @php
                        $mapsQuery = urlencode($supplier->name . ' ' . $supplier->city . ' ' . $supplier->country);
                    @endphp
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $mapsQuery }}" target="_blank" rel="noopener noreferrer" class="mt-2 text-xs font-semibold text-black hover:underline inline-flex items-center gap-1">
                        Buka di peta <x-heroicon-m-arrow-top-right-on-square class="w-3.5 h-3.5" />
                    </a>
                </div>
            </aside>
        </div>

        <!-- Bottom: Pemasok Serupa -->
        @if(isset($similarSuppliers) && $similarSuppliers->isNotEmpty())
            <div class="mt-16 pt-12 border-t border-gray-200">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-bold text-gray-900">Pemasok serupa</h2>
                    <a href="{{ route('discover.index', ['category' => $supplier->industry_category]) }}" class="text-xs font-semibold text-gray-900 hover:underline">
                        Lihat semua {{ strtolower($supplier->industry_category) }} &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($similarSuppliers as $sim)
                        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-2xs hover:shadow-md transition">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-black text-white font-bold flex items-center justify-center text-sm shrink-0">
                                    {{ strtoupper(substr($sim->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-sm text-gray-900 truncate">
                                        <a href="{{ route('suppliers.show', $sim->slug) }}" class="hover:underline">{{ $sim->name }}</a>
                                    </h3>
                                    <p class="text-2xs text-gray-500 mt-0.5 truncate">{{ $sim->city ? $sim->city . ' &bull; ' : '' }}{{ $sim->country }}</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @foreach($sim->tags->take(2) as $t)
                                    <span class="text-2xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">{{ $t->tag_name }}</span>
                                @endforeach
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                                <span class="text-gray-500 text-2xs">MOQ: {{ $sim->moq ?? '5.000 unit' }}</span>
                                <a href="{{ route('suppliers.show', $sim->slug) }}" class="font-semibold text-black hover:underline text-2xs">Lihat profil &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
