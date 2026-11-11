@props([
    'supplier',
    'selectable' => false,
    'selected' => false,
    'limitReached' => false,
])

@php
    $totalDocsCount = $supplier->documents_count ?? $supplier->documents->count();
    $visibleTags = $supplier->tags->take(3);
    $remainingTagsCount = max(0, $supplier->tags->count() - 3);
    $primaryCert = $supplier->certifications->isNotEmpty()
        ? ($supplier->certifications->first()->certification?->name ?? 'Tersedia')
        : 'Tersedia';
    $locationLabel = ($supplier->city ? $supplier->city . ', ' : '') . $supplier->country;
@endphp

<article {{ $attributes->merge([
    'class' => 'nb-card-interactive p-5 sm:p-6 h-full flex flex-col justify-between relative ' . (
        $selected ? 'ring-2 ring-brand-600 border-brand-600 bg-brand-50/10' : ''
    )
]) }}>
    <div>
        <!-- Zona 1: Header [Avatar] [Nama + Meta] ... [Simpan] -->
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-3.5 min-w-0 flex-1">
                <x-ui.avatar :name="$supplier->name" :logo="$supplier->logo" />

                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 min-w-0">
                        <h3 class="font-semibold text-text-primary text-base sm:text-lg leading-snug truncate"
                            title="{{ $supplier->name }}">
                            <a href="{{ route('suppliers.show', $supplier->slug) }}"
                               class="hover:underline rounded nb-focus-ring">
                                {{ $supplier->name }}
                            </a>
                        </h3>
                        @if($supplier->average_rating)
                            <span class="inline-flex items-center gap-1 text-xs bg-amber-50 border border-amber-200 text-amber-900 font-semibold px-2 py-0.5 rounded-full shrink-0">
                                <x-heroicon-s-star class="w-3.5 h-3.5 text-amber-500 fill-amber-500" />
                                <span>{{ number_format($supplier->average_rating, 1) }}</span>
                            </span>
                        @endif
                    </div>

                    <p class="text-13 text-text-secondary mt-1 flex items-center gap-1.5 min-w-0">
                        <span class="truncate">{{ $supplier->industry_category }}</span>
                        <span class="text-text-muted shrink-0" aria-hidden="true">&middot;</span>
                        <span class="inline-flex items-center gap-1 min-w-0 truncate" title="{{ $locationLabel }}">
                            <x-heroicon-o-map-pin class="w-3.5 h-3.5 text-text-muted shrink-0" />
                            <span class="truncate">{{ $locationLabel }}</span>
                        </span>
                    </p>
                </div>
            </div>

            <!-- Action: Bookmark -->
            <div class="shrink-0">
                @auth
                    <form method="POST" action="{{ route('suppliers.save', $supplier) }}">
                        @csrf
                        <button type="submit"
                                aria-label="Simpan {{ $supplier->name }}"
                                title="Simpan pemasok"
                                class="w-10 h-10 inline-flex items-center justify-center text-text-muted hover:text-text-primary rounded-xl hover:bg-surface-subtle transition nb-focus-ring">
                            <x-heroicon-o-bookmark class="w-5 h-5" />
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       aria-label="Masuk untuk menyimpan {{ $supplier->name }}"
                       title="Masuk untuk menyimpan"
                       class="w-10 h-10 inline-flex items-center justify-center text-text-muted hover:text-text-primary rounded-xl hover:bg-surface-subtle transition nb-focus-ring">
                        <x-heroicon-o-bookmark class="w-5 h-5" />
                    </a>
                @endauth
            </div>
        </div>

        <!-- Zona 2: Chip Kategori (1 baris, tinggi seragam 24px) -->
        <div class="flex items-center gap-1.5 mt-4 h-6 overflow-hidden whitespace-nowrap">
            @forelse($visibleTags as $tag)
                <x-ui.chip class="max-w-[140px] truncate" title="{{ $tag->tag_name }}">
                    {{ $tag->tag_name }}
                </x-ui.chip>
            @empty
                <x-ui.chip>
                    {{ $supplier->industry_category }}
                </x-ui.chip>
            @endforelse
            @if($remainingTagsCount > 0)
                <x-ui.chip variant="muted" title="{{ $remainingTagsCount }} spesialisasi lainnya">
                    +{{ $remainingTagsCount }}
                </x-ui.chip>
            @endif
        </div>

        <!-- Zona 3: Metrik Operasional Grid 2x2 -->
        <div class="grid grid-cols-2 gap-x-4 gap-y-3 bg-surface rounded-xl p-3.5 mt-4 border border-border/80">
            <x-ui.metric label="Kapasitas" :value="$supplier->production_capacity ?? 'Tersedia'" />
            <x-ui.metric label="MOQ" :value="$supplier->moq ?? 'Fleksibel'" />
            <x-ui.metric label="Lead time" :value="$supplier->avg_lead_time ?? '2-4 minggu'" />
            <x-ui.metric label="Sertifikasi" :value="$primaryCert" />
        </div>

        <!-- Zona 4: Bukti Kepercayaan (WCAG AA) -->
        <div class="mt-3.5 flex items-center justify-between gap-2 text-13 text-text-secondary">
            <span class="inline-flex items-center gap-1.5 min-w-0 truncate">
                <x-heroicon-o-document-check class="w-4 h-4 text-brand-600 shrink-0" />
                <span class="truncate">{{ $totalDocsCount }} berkas tervalidasi</span>
            </span>
            <span class="inline-flex items-center gap-1 text-text-secondary shrink-0">
                <x-heroicon-o-clock class="w-4 h-4 text-text-muted shrink-0" />
                <span>Respons: &lt; 2 jam</span>
            </span>
        </div>
    </div>

    <!-- Zona 5: Footer Aksi & Toggle Perbandingan (Menempel di Bawah) -->
    <div class="mt-5 pt-4 border-t border-border/80 space-y-2.5">
        <div class="grid grid-cols-2 gap-2.5">
            <a href="{{ route('suppliers.show', $supplier->slug) }}"
               class="h-11 px-3.5 text-center text-13 font-semibold text-white bg-gray-900 hover:bg-black rounded-xl transition inline-flex items-center justify-center gap-1.5 shadow-2xs nb-focus-ring">
                <span>Lihat profil</span>
                <x-heroicon-m-arrow-right class="w-4 h-4 shrink-0" />
            </a>

            @if($supplier->whatsapp_url)
                <a href="{{ $supplier->whatsapp_url }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="h-11 px-3.5 text-center text-13 font-semibold text-brand-800 bg-brand-50 hover:bg-brand-100 border border-brand-200 rounded-xl transition inline-flex items-center justify-center gap-1.5 nb-focus-ring">
                    <svg class="w-4 h-4 fill-current text-brand-600 shrink-0" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.153.57 4.173 1.564 5.918l-1.564 5.71 5.86-1.536c1.677.915 3.597 1.408 5.64 1.408 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                    <span>WhatsApp</span>
                </a>
            @else
                <a href="mailto:{{ $supplier->email ?? 'info@namabiz.com' }}"
                   class="h-11 px-3.5 text-center text-13 font-semibold text-text-primary bg-surface hover:bg-surface-subtle border border-border rounded-xl transition inline-flex items-center justify-center gap-1.5 nb-focus-ring">
                    <x-heroicon-o-envelope class="w-4 h-4 text-text-secondary shrink-0" />
                    <span>Email</span>
                </a>
            @endif
        </div>

        @if($selectable)
            <x-ui.compare-toggle
                :supplier-id="$supplier->id"
                :supplier-name="$supplier->name"
                :selected="$selected"
                :limit-reached="$limitReached"
            />
        @endif
    </div>
</article>
