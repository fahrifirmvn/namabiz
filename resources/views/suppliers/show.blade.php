<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeTab: '{{ request('tab', 'ringkasan') }}' }">
        <!-- Breadcrumbs -->
        <nav class="text-xs text-gray-500 mb-6 flex items-center gap-2">
            <a href="{{ route('discover.index') }}" class="hover:text-gray-900">Jelajahi</a>
            <span>/</span>
            <a href="{{ route('discover.index', ['category' => $supplier->industry_category]) }}" class="hover:text-gray-900">{{ $supplier->industry_category }}</a>
            <span>/</span>
            <span class="font-semibold text-gray-900 truncate max-w-xs">{{ $supplier->name }}</span>
        </nav>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                <x-heroicon-s-check-circle class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

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

                            @if($supplier->average_rating)
                                <button type="button" @click="activeTab = 'ulasan'" class="inline-flex items-center gap-1.5 text-xs bg-amber-50 border border-amber-200 text-amber-900 font-bold px-2.5 py-0.5 rounded-full hover:bg-amber-100 transition">
                                    <x-heroicon-s-star class="w-3.5 h-3.5 text-amber-500 fill-amber-500" />
                                    <span>{{ number_format($supplier->average_rating, 1) }}</span>
                                    <span class="text-gray-400 font-normal">({{ $supplier->reviews_count }} ulasan)</span>
                                </button>
                            @else
                                <button type="button" @click="activeTab = 'ulasan'" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-black transition">
                                    <x-heroicon-o-star class="w-3.5 h-3.5" />
                                    <span>Belum ada ulasan</span>
                                </button>
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
                            @if($supplier->whatsapp)
                                <a href="{{ $supplier->whatsapp_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-800 font-medium">
                                    <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4 text-emerald-600" /> {{ $supplier->whatsapp }}
                                </a>
                            @endif
                            @if($supplier->email)
                                <a href="mailto:{{ $supplier->email }}" class="inline-flex items-center gap-1 text-gray-700 hover:text-black">
                                    <x-heroicon-o-envelope class="w-4 h-4 text-gray-400" /> {{ $supplier->email }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Action Buttons -->
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 shrink-0">
                    @if($supplier->whatsapp_url)
                        <a href="{{ $supplier->whatsapp_url }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2">
                            <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4" />
                            <span>WhatsApp</span>
                        </a>
                    @endif

                    @php
                        $contactEmail = $supplier->email ?: ($supplier->website ? 'sales@' . ltrim(parse_url($websiteUrl ?? $supplier->website, PHP_URL_HOST) ?? $supplier->website, 'www.') : 'contact@namabiz.com');
                    @endphp
                    <a href="mailto:{{ $contactEmail }}"
                       class="px-4 py-2.5 bg-black hover:bg-gray-800 text-white font-medium text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2">
                        <x-heroicon-o-envelope class="w-4 h-4" />
                        <span>Kirim Email</span>
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
            <div class="mt-8 pt-4 border-t border-gray-100 flex items-center gap-8 text-sm font-medium overflow-x-auto">
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
                <button type="button" @click="activeTab = 'ulasan'"
                        :class="activeTab === 'ulasan' ? 'border-b-2 border-black font-bold text-gray-900 pb-2' : 'text-gray-500 hover:text-gray-900 pb-2'">
                    Ulasan ({{ $supplier->reviews_count }})
                </button>
            </div>
        </div>

        <!-- 2 Column Layout (Content & Sidebar) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Main Column (2 spans) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- 1. Tentang (About) -->
                <div x-show="['ringkasan', 'kemampuan'].includes(activeTab)" class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Tentang</h2>
                    <div class="text-sm text-gray-700 leading-relaxed space-y-3">
                        <p>{{ $supplier->about ?: ($supplier->description ?: 'Pemasok terpercaya terdaftar di jaringan Namabiz dengan spesialisasi industri dan sertifikasi resmi.') }}</p>
                    </div>
                </div>

                <!-- 2. Kemampuan Inti -->
                <div x-show="['ringkasan', 'kemampuan'].includes(activeTab)" class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs">
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
                <div x-show="['ringkasan', 'kemampuan'].includes(activeTab)" class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs relative">
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
                <div x-show="['ringkasan', 'sertifikasi'].includes(activeTab)" class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs">
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
                            <div class="col-span-full py-6 text-center text-xs text-gray-400 bg-surface rounded-xl border border-dashed border-gray-200">
                                Belum ada sertifikasi yang tercatat untuk pemasok ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- 5. Dokumen Tab Content -->
                <div x-show="activeTab === 'dokumen'" class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Pustaka Dokumen & Brosur Teknis</h2>
                            <p class="text-xs text-gray-500 mt-1">Katalog produk, spesifikasi material, dan sertifikat tervalidasi pemasok.</p>
                        </div>
                        <span class="text-xs font-semibold bg-gray-100 text-gray-700 px-3 py-1 rounded-full">
                            {{ $supplier->documents->count() }} Dokumen
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse($supplier->documents as $doc)
                            <div class="flex items-center justify-between p-4 rounded-xl bg-surface border border-gray-200 hover:border-gray-300 transition">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                        <x-heroicon-o-document-text class="w-5 h-5 text-gray-600" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $doc->title }}</p>
                                        <p class="text-2xs text-gray-500 uppercase mt-0.5">{{ $doc->type ?? 'PDF' }} &bull; Dokumen Resmi</p>
                                    </div>
                                </div>
                                <div class="shrink-0 ml-4">
                                    @if(!$doc->is_premium || auth()->user()?->isPremium())
                                        <a href="{{ route('documents.download', $doc) }}" class="px-4 py-2 bg-black hover:bg-gray-800 text-white rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition">
                                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                                            <span>Unduh</span>
                                        </a>
                                    @else
                                        <a href="{{ route('upgrade') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition">
                                            <x-heroicon-s-lock-closed class="w-4 h-4" />
                                            <span>Buka Akses</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 text-xs">
                                Belum ada dokumen publik yang diunggah oleh pemasok ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- 6. Tab Ulasan & Evaluasi Rekanan -->
                <div x-show="activeTab === 'ulasan'" class="space-y-8">
                    @php
                        $totalReviews = $reviewStats['count'];
                        $avgRating = $reviewStats['average'];
                        $avgQuality = $reviewStats['quality'];
                        $avgDelivery = $reviewStats['delivery'];
                        $avgService = $reviewStats['service'];
                        $breakdown = $reviewStats['breakdown'];
                    @endphp

                    <!-- Summary Scorecard -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8 pb-6 border-b border-gray-100">
                            <!-- Overall Score -->
                            <div class="flex items-center gap-5">
                                <div class="text-center sm:text-left">
                                    <div class="text-5xl font-extrabold text-gray-900 tracking-tight">
                                        {{ $avgRating ? number_format($avgRating, 1) : '0.0' }}
                                    </div>
                                    <div class="flex items-center justify-center sm:justify-start gap-1 mt-2 text-amber-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($avgRating && $i <= round($avgRating))
                                                <x-heroicon-s-star class="w-5 h-5 fill-amber-400 text-amber-400" />
                                            @else
                                                <x-heroicon-s-star class="w-5 h-5 text-gray-200 fill-gray-200" />
                                            @endif
                                        @endfor
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Berdasarkan {{ $totalReviews }} ulasan pembeli</p>
                                </div>
                            </div>

                            <!-- Rating Distribution -->
                            <div class="w-full lg:max-w-xs space-y-1.5 text-xs">
                                @foreach([5, 4, 3, 2, 1] as $star)
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 text-gray-600 font-medium">{{ $star }} ★</span>
                                        <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-amber-400 rounded-full" style="width: {{ $breakdown[$star] ?? 0 }}%"></div>
                                        </div>
                                        <span class="w-8 text-right text-2xs text-gray-400">{{ $breakdown[$star] ?? 0 }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Parameter Ratings -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6">
                            <div class="bg-surface rounded-xl p-4 border border-gray-100 text-center">
                                <span class="text-2xs uppercase tracking-wider text-gray-400 font-semibold block">Kualitas Bahan & Produk</span>
                                <div class="text-2xl font-extrabold text-gray-900 mt-1">
                                    {{ $avgQuality ? number_format($avgQuality, 1) : '-' }} <span class="text-xs text-gray-400 font-normal">/ 5</span>
                                </div>
                                <span class="text-2xs text-gray-500">Standar mutu presisi</span>
                            </div>

                            <div class="bg-surface rounded-xl p-4 border border-gray-100 text-center">
                                <span class="text-2xs uppercase tracking-wider text-gray-400 font-semibold block">Ketepatan Lead Time</span>
                                <div class="text-2xl font-extrabold text-gray-900 mt-1">
                                    {{ $avgDelivery ? number_format($avgDelivery, 1) : '-' }} <span class="text-xs text-gray-400 font-normal">/ 5</span>
                                </div>
                                <span class="text-2xs text-gray-500">Kesiapan pengiriman & jadwal</span>
                            </div>

                            <div class="bg-surface rounded-xl p-4 border border-gray-100 text-center">
                                <span class="text-2xs uppercase tracking-wider text-gray-400 font-semibold block">Layanan & Komunikasi</span>
                                <div class="text-2xl font-extrabold text-gray-900 mt-1">
                                    {{ $avgService ? number_format($avgService, 1) : '-' }} <span class="text-xs text-gray-400 font-normal">/ 5</span>
                                </div>
                                <span class="text-2xs text-gray-500">Kecepatan respons sales</span>
                            </div>
                        </div>
                    </div>

                    <!-- Review Form Box -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs">
                        @auth
                            <h3 class="text-lg font-bold text-gray-900 mb-2">
                                {{ $userReview ? 'Perbarui Evaluasi & Ulasan Anda' : 'Tulis Evaluasi & Ulasan Rekanan' }}
                            </h3>
                            <p class="text-xs text-gray-500 mb-6">
                                Bagikan pengalaman pengadaan Anda untuk membantu rekan procurement lain dalam memilih vendor terbaik.
                            </p>

                            <form method="POST" action="{{ route('suppliers.reviews.store', $supplier->slug) }}"
                                  x-data="{
                                      rating: {{ old('rating', $userReview?->rating ?? 5) }},
                                      quality_rating: {{ old('quality_rating', $userReview?->quality_rating ?? 5) }},
                                      delivery_rating: {{ old('delivery_rating', $userReview?->delivery_rating ?? 5) }},
                                      service_rating: {{ old('service_rating', $userReview?->service_rating ?? 5) }}
                                  }"
                                  class="space-y-5">
                                @csrf

                                <!-- Overall Rating Selector -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-800 mb-1">Skor Keseluruhan</label>
                                    <div class="flex items-center gap-1.5">
                                        <template x-for="star in [1, 2, 3, 4, 5]">
                                            <button type="button" @click="rating = star" class="p-1 text-gray-300 hover:scale-110 transition focus:outline-hidden">
                                                <svg class="w-8 h-8 transition-colors" :class="star <= rating ? 'text-amber-400 fill-amber-400' : 'text-gray-300 fill-gray-200'" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </button>
                                        </template>
                                        <span class="text-sm font-bold text-gray-800 ml-2" x-text="rating + ' dari 5 Bintang'"></span>
                                    </div>
                                    <input type="hidden" name="rating" :value="rating">
                                    @error('rating') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Sub Criteria Ratings -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                                    <!-- Kualitas Produk -->
                                    <div class="bg-gray-50/80 p-3.5 rounded-xl border border-gray-200">
                                        <label class="block text-2xs uppercase tracking-wider font-bold text-gray-700 mb-2">Mutu & Kualitas</label>
                                        <div class="flex items-center gap-1.5">
                                            <template x-for="q in [1, 2, 3, 4, 5]">
                                                <button type="button" @click="quality_rating = q"
                                                        :class="quality_rating >= q ? 'bg-amber-400 text-white shadow-xs' : 'bg-white text-gray-600 border border-gray-200'"
                                                        class="w-7 h-7 rounded-lg text-xs font-bold flex items-center justify-center transition">
                                                    <span x-text="q"></span>
                                                </button>
                                            </template>
                                        </div>
                                        <input type="hidden" name="quality_rating" :value="quality_rating">
                                        @error('quality_rating') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <!-- Lead Time -->
                                    <div class="bg-gray-50/80 p-3.5 rounded-xl border border-gray-200">
                                        <label class="block text-2xs uppercase tracking-wider font-bold text-gray-700 mb-2">Ketepatan Lead Time</label>
                                        <div class="flex items-center gap-1.5">
                                            <template x-for="d in [1, 2, 3, 4, 5]">
                                                <button type="button" @click="delivery_rating = d"
                                                        :class="delivery_rating >= d ? 'bg-amber-400 text-white shadow-xs' : 'bg-white text-gray-600 border border-gray-200'"
                                                        class="w-7 h-7 rounded-lg text-xs font-bold flex items-center justify-center transition">
                                                    <span x-text="d"></span>
                                                </button>
                                            </template>
                                        </div>
                                        <input type="hidden" name="delivery_rating" :value="delivery_rating">
                                        @error('delivery_rating') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <!-- Layanan & Respons -->
                                    <div class="bg-gray-50/80 p-3.5 rounded-xl border border-gray-200">
                                        <label class="block text-2xs uppercase tracking-wider font-bold text-gray-700 mb-2">Layanan & Komunikasi</label>
                                        <div class="flex items-center gap-1.5">
                                            <template x-for="s in [1, 2, 3, 4, 5]">
                                                <button type="button" @click="service_rating = s"
                                                        :class="service_rating >= s ? 'bg-amber-400 text-white shadow-xs' : 'bg-white text-gray-600 border border-gray-200'"
                                                        class="w-7 h-7 rounded-lg text-xs font-bold flex items-center justify-center transition">
                                                    <span x-text="s"></span>
                                                </button>
                                            </template>
                                        </div>
                                        <input type="hidden" name="service_rating" :value="service_rating">
                                        @error('service_rating') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <!-- Review Title -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-800 mb-1">Judul Ulasan</label>
                                    <input type="text" name="title" value="{{ old('title', $userReview?->title) }}"
                                           placeholder="Contoh: Kualitas cetak packaging presisi dan konsisten"
                                           class="w-full text-xs rounded-xl border border-gray-200 px-3.5 py-2.5 focus:border-black focus:ring-black" required>
                                    @error('title') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Comment Body -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-800 mb-1">Pengalaman & Evaluasi Detail</label>
                                    <textarea name="comment" rows="4"
                                              placeholder="Ceritakan detail performa vendor seperti kestabilan batch, kecepatan sampel, toleransi warna, atau penanganan kendala..."
                                              class="w-full text-xs rounded-xl border border-gray-200 p-3.5 focus:border-black focus:ring-black leading-relaxed" required>{{ old('comment', $userReview?->comment) }}</textarea>
                                    @error('comment') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Reviewer Identity / Company & Role -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-800 mb-1">Nama Perusahaan Pembeli (Opsional)</label>
                                        <input type="text" name="reviewer_company" value="{{ old('reviewer_company', $userReview?->reviewer_company) }}"
                                               placeholder="Contoh: PT Sumber Pangan Nusantara"
                                               class="w-full text-xs rounded-xl border border-gray-200 px-3.5 py-2.5 focus:border-black focus:ring-black">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-800 mb-1">Jabatan / Role (Opsional)</label>
                                        <input type="text" name="reviewer_role" value="{{ old('reviewer_role', $userReview?->reviewer_role) }}"
                                               placeholder="Contoh: Senior Procurement Specialist"
                                               class="w-full text-xs rounded-xl border border-gray-200 px-3.5 py-2.5 focus:border-black focus:ring-black">
                                    </div>
                                </div>

                                @if(auth()->user()->isPremium())
                                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs text-emerald-800">
                                        <x-heroicon-s-check-badge class="w-4 h-4 text-emerald-600 shrink-0" />
                                        <span>Sebagai pengguna <strong>Premium</strong>, ulasan Anda akan otomatis bertanda <strong>Pembeli Terverifikasi</strong>.</span>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between pt-2">
                                    <button type="submit" class="px-5 py-2.5 bg-black hover:bg-gray-800 text-white text-xs font-bold rounded-xl transition shadow-xs inline-flex items-center gap-2">
                                        <x-heroicon-o-paper-airplane class="w-4 h-4" />
                                        <span>{{ $userReview ? 'Simpan Perubahan Ulasan' : 'Publikasikan Ulasan' }}</span>
                                    </button>

                                    @if($userReview)
                                        <button type="submit" form="delete-own-review-form" class="text-xs text-rose-600 hover:text-rose-800 font-semibold underline">
                                            Hapus ulasan saya
                                        </button>
                                    @endif
                                </div>
                            </form>

                            @if($userReview)
                                <form id="delete-own-review-form" method="POST" action="{{ route('suppliers.reviews.destroy', [$supplier->slug, $userReview]) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?')" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        @else
                            <div class="text-center py-6">
                                <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-600 flex items-center justify-center mx-auto mb-3">
                                    <x-heroicon-o-pencil-square class="w-6 h-6" />
                                </div>
                                <h4 class="font-bold text-base text-gray-900">Pernah bertransaksi dengan {{ $supplier->name }}?</h4>
                                <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">
                                    Masuk ke akun Anda untuk memberikan ulasan bintang, evaluasi mutu produk, dan ketepatan pengiriman rekanan.
                                </p>
                                <div class="mt-4">
                                    <a href="{{ route('login') }}" class="px-5 py-2.5 bg-black hover:bg-gray-800 text-white rounded-xl text-xs font-bold inline-flex items-center gap-2 shadow-xs transition">
                                        Masuk untuk Memberikan Ulasan
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>

                    <!-- Reviews List Feed -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-base text-gray-900">Ulasan Pembeli ({{ $supplier->reviews_count }})</h3>
                        </div>

                        @forelse($supplier->reviews as $rev)
                            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs space-y-4">
                                <!-- Reviewer Header -->
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-black text-white font-bold flex items-center justify-center text-sm shrink-0">
                                            {{ strtoupper(substr($rev->user?->name ?? 'User', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-bold text-sm text-gray-900">{{ $rev->user?->name ?? 'Pembeli Rekanan' }}</span>
                                                @if($rev->is_verified_buyer)
                                                    <span class="inline-flex items-center gap-1 text-2xs bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 rounded-full font-semibold">
                                                        <x-heroicon-s-check-badge class="w-3.5 h-3.5 text-emerald-600" /> Pembeli Terverifikasi
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-2xs text-gray-500 mt-0.5">
                                                @if($rev->reviewer_role || $rev->reviewer_company)
                                                    {{ $rev->reviewer_role ? $rev->reviewer_role . ' di ' : '' }}{{ $rev->reviewer_company ?? '' }} &bull;
                                                @endif
                                                {{ $rev->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>

                                    @if(auth()->check() && (auth()->id() === $rev->user_id || auth()->user()->isAdmin()))
                                        <form method="POST" action="{{ route('suppliers.reviews.destroy', [$supplier->slug, $rev]) }}"
                                              onsubmit="return confirm('Hapus ulasan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-2xs text-gray-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-gray-50 transition" title="Hapus ulasan">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <!-- Star Ratings & Sub-metrics -->
                                <div>
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center text-amber-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $rev->rating)
                                                    <x-heroicon-s-star class="w-4 h-4 fill-amber-400 text-amber-400" />
                                                @else
                                                    <x-heroicon-s-star class="w-4 h-4 text-gray-200 fill-gray-200" />
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="font-bold text-xs text-gray-900">{{ $rev->rating }}.0</span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                        <span class="text-2xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-md font-medium">
                                            Kualitas: <strong>{{ $rev->quality_rating }}/5</strong>
                                        </span>
                                        <span class="text-2xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-md font-medium">
                                            Lead Time: <strong>{{ $rev->delivery_rating }}/5</strong>
                                        </span>
                                        <span class="text-2xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-md font-medium">
                                            Layanan: <strong>{{ $rev->service_rating }}/5</strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- Review Title & Content -->
                                <div>
                                    <h4 class="font-bold text-sm text-gray-900 mb-1">{{ $rev->title }}</h4>
                                    <p class="text-xs text-gray-700 leading-relaxed">{{ $rev->comment }}</p>
                                </div>

                                <!-- Official Supplier Reply if available -->
                                @if($rev->supplier_reply)
                                    <div class="mt-4 p-4 rounded-xl bg-gray-50 border-l-4 border-black space-y-1.5">
                                        <div class="flex items-center justify-between text-2xs">
                                            <span class="font-bold text-gray-900 inline-flex items-center gap-1.5">
                                                <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-black" />
                                                Tanggapan Resmi {{ $supplier->name }}
                                            </span>
                                            <span class="text-gray-400">{{ $rev->replied_at?->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-xs text-gray-700 leading-relaxed">{{ $rev->supplier_reply }}</p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center shadow-xs">
                                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                    <x-heroicon-o-chat-bubble-bottom-center-text class="w-6 h-6" />
                                </div>
                                <h4 class="font-bold text-sm text-gray-900">Belum ada ulasan untuk vendor ini</h4>
                                <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                                    Jadilah pembeli pertama yang membagikan pengalaman pengadaan produk atau manufaktur bersama {{ $supplier->name }}.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Preview Ulasan di Tab Ringkasan -->
                <div x-show="activeTab === 'ringkasan'" class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-gray-900">Ulasan & Reputasi</h2>
                            @if($supplier->average_rating)
                                <span class="inline-flex items-center gap-1 text-xs bg-amber-50 text-amber-900 border border-amber-200 px-2 py-0.5 rounded-full font-bold">
                                    ★ {{ number_format($supplier->average_rating, 1) }}
                                </span>
                            @endif
                        </div>
                        <button type="button" @click="activeTab = 'ulasan'" class="text-xs font-bold text-black hover:underline">
                            Lihat semua ({{ $supplier->reviews_count }}) &rarr;
                        </button>
                    </div>

                    @if($supplier->reviews->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($supplier->reviews->take(2) as $prevRev)
                                <div class="p-4 rounded-xl bg-surface border border-gray-100">
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span class="font-bold text-gray-900">{{ $prevRev->user?->name ?? 'Pembeli Rekanan' }}</span>
                                        <span class="text-2xs text-gray-400">{{ $prevRev->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center text-amber-400 text-xs mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="{{ $i <= $prevRev->rating ? 'text-amber-400' : 'text-gray-200' }}">★</span>
                                        @endfor
                                        <span class="ml-1.5 font-semibold text-gray-700 text-2xs">{{ $prevRev->title }}</span>
                                    </div>
                                    <p class="text-xs text-gray-600 line-clamp-2">{{ $prevRev->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-500">Belum ada ulasan pembeli. Klik di bawah untuk menulis ulasan pertama.</p>
                        <button type="button" @click="activeTab = 'ulasan'" class="mt-3 text-xs font-semibold text-black underline">
                            Tulis ulasan sekarang &rarr;
                        </button>
                    @endif
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
                                        <button type="button" x-on:click="Livewire.dispatch('open-premium-modal', { documentTitle: @js($doc->title) })" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Terkunci untuk akun Premium">
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
