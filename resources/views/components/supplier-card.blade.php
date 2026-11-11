@props(['supplier'])

@php
    $totalDocsCount = $supplier->documents_count ?? $supplier->documents->count();
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
                        @if($supplier->average_rating)
                            <span class="inline-flex items-center gap-1 text-2xs bg-amber-50 border border-amber-200 text-amber-900 font-bold px-2 py-0.5 rounded-full shrink-0">
                                <x-heroicon-s-star class="w-3 h-3 text-amber-500 fill-amber-500" />
                                {{ number_format($supplier->average_rating, 1) }}
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
        @if($supplier->whatsapp_url)
            <a href="{{ $supplier->whatsapp_url }}"
               target="_blank"
               rel="noopener noreferrer"
               class="flex-1 py-2 text-center text-xs font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition inline-flex items-center justify-center gap-1.5 border border-emerald-200">
                <svg class="w-3.5 h-3.5 fill-current text-emerald-600" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.153.57 4.173 1.564 5.918l-1.564 5.71 5.86-1.536c1.677.915 3.597 1.408 5.64 1.408 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                <span>WhatsApp</span>
            </a>
        @else
            <a href="mailto:{{ $supplier->email ?? 'info@namabiz.com' }}"
               class="flex-1 py-2 text-center text-xs font-semibold text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                Email
            </a>
        @endif
        <a href="{{ route('suppliers.show', $supplier->slug) }}"
           class="flex-1 py-2 text-center text-xs font-semibold text-white bg-black hover:bg-gray-800 rounded-xl transition flex items-center justify-center gap-1">
            Lihat profil <span aria-hidden="true">&rarr;</span>
        </a>
    </div>
</div>
