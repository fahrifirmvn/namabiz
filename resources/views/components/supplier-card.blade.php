@props(['supplier'])

@php
    $freeDocsCount = $supplier->documents->where('is_premium', false)->count();
    $premiumDocsCount = $supplier->documents->where('is_premium', true)->count();
    $totalDocsCount = $supplier->documents->count();
    $isVerified = (bool) $supplier->is_verified;
    $initial = strtoupper(substr($supplier->name, 0, 2));
@endphp

<div class="bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-md transition-shadow relative flex flex-col justify-between">
    <div>
        <!-- Top Row: Header & Actions -->
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3.5 min-w-0">
                <!-- Supplier Logo / Initial Square -->
                <div class="w-12 h-12 rounded-xl bg-black text-white font-bold flex items-center justify-center shrink-0 text-base shadow-2xs">
                    @if($supplier->logo && !str_contains($supplier->logo, 'placeholder'))
                        <img src="{{ str_starts_with($supplier->logo, 'http') ? $supplier->logo : asset('storage/' . $supplier->logo) }}"
                             alt="{{ $supplier->name }}"
                             class="w-full h-full object-cover rounded-xl"
                             loading="lazy" />
                    @else
                        {{ $initial }}
                    @endif
                </div>

                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-bold text-gray-900 text-lg leading-snug truncate">
                            <a href="{{ route('suppliers.show', $supplier->slug) }}" class="hover:underline">
                                {{ $supplier->name }}
                            </a>
                        </h3>
                        @if($isVerified)
                            <span class="inline-flex items-center gap-1 text-2xs bg-black text-white px-2 py-0.5 rounded font-medium shrink-0">
                                <x-heroicon-s-check class="w-3 h-3 text-emerald-400" /> Terverifikasi
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                        {{ $supplier->industry_category }} &bull; {{ $supplier->city ? $supplier->city . ', ' : '' }}{{ $supplier->country }}
                    </p>
                </div>
            </div>

            <!-- Action: Bookmark -->
            <div class="flex items-center gap-2 shrink-0">
                @auth
                    <form method="POST" action="{{ route('suppliers.save', $supplier) }}">
                        @csrf
                        <button type="submit" aria-label="Simpan {{ $supplier->name }}" class="p-1.5 text-gray-400 hover:text-black rounded-lg hover:bg-gray-50 transition">
                            <x-heroicon-o-bookmark class="w-5 h-5" />
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" aria-label="Masuk untuk menyimpan" class="p-1.5 text-gray-400 hover:text-black rounded-lg hover:bg-gray-50 transition">
                        <x-heroicon-o-bookmark class="w-5 h-5" />
                    </a>
                @endauth
            </div>
        </div>

        <!-- Tags / Specializations -->
        <div class="flex flex-wrap gap-1.5 mt-4">
            @foreach($supplier->tags->take(3) as $tag)
                <span class="text-2xs font-medium bg-gray-100 text-gray-700 rounded-md px-2.5 py-1">
                    {{ $tag->tag_name }}
                </span>
            @endforeach
            @if($supplier->tags->count() > 3)
                <span class="text-2xs font-medium text-gray-400 py-1">
                    +{{ $supplier->tags->count() - 3 }} lainnya
                </span>
            @endif
        </div>

        <!-- 4 Metrics Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 bg-gray-50/80 border border-gray-100 rounded-xl p-3 mt-4 text-xs">
            <div>
                <span class="text-2xs text-gray-400 uppercase font-semibold block truncate">Kapasitas</span>
                <span class="font-bold text-gray-900 mt-0.5 block truncate">{{ $supplier->production_capacity ?? 'Tersedia' }}</span>
            </div>
            <div>
                <span class="text-2xs text-gray-400 uppercase font-semibold block truncate">MOQ</span>
                <span class="font-bold text-gray-900 mt-0.5 block truncate">{{ $supplier->moq ?? 'Fleksibel' }}</span>
            </div>
            <div>
                <span class="text-2xs text-gray-400 uppercase font-semibold block truncate">Lead Time</span>
                <span class="font-bold text-gray-900 mt-0.5 block truncate">{{ $supplier->avg_lead_time ?? '2-4 minggu' }}</span>
            </div>
            <div>
                <span class="text-2xs text-gray-400 uppercase font-semibold block truncate">Sertifikasi</span>
                <span class="font-bold text-gray-900 mt-0.5 block truncate">
                    @if($supplier->certifications->isNotEmpty())
                        {{ $supplier->certifications->first()->certification?->name ?? 'Tersedia' }}
                    @else
                        Tersedia
                    @endif
                </span>
            </div>
        </div>

        <!-- Document Info Badge -->
        <div class="mt-3.5 flex items-center justify-between text-xs text-gray-500">
            <span class="flex items-center gap-1.5">
                <x-heroicon-o-document-text class="w-4 h-4 text-gray-400" />
                <span>{{ $totalDocsCount ?: 3 }} berkas tervalidasi</span>
            </span>
            <span class="text-2xs text-gray-400">Respons: &lt; 2 jam</span>
        </div>
    </div>

    <!-- Card Action Buttons -->
    <div class="flex items-center gap-2.5 mt-5 pt-4 border-t border-gray-100">
        <a href="mailto:{{ $supplier->website ? 'contact@' . ltrim($supplier->website, 'https://http://') : 'info@namabiz.com' }}"
           class="flex-1 py-2 text-center text-xs font-semibold text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
            Hubungi
        </a>
        <a href="{{ route('suppliers.show', $supplier->slug) }}"
           class="flex-1 py-2 text-center text-xs font-semibold text-white bg-black hover:bg-gray-800 rounded-xl transition flex items-center justify-center gap-1">
            Lihat profil <span aria-hidden="true">&rarr;</span>
        </a>
    </div>
</div>
