@props(['suppliers', 'lowestMoq' => null])
<section {{ $attributes->merge(['class' => 'nb-comparison-preview min-w-0']) }} aria-labelledby="comparison-heading" data-reveal>
    <div class="nb-comparison-top">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-border bg-surface text-brand-700"><x-heroicon-o-scale class="h-5 w-5" /></span>
            <div>
                <p class="nb-eyebrow mb-0.5">KENALI PILIHAN ANDA</p>
                <h2 id="comparison-heading" class="text-base font-bold text-text-primary">Pratinjau Lembar Komparasi</h2>
            </div>
        </div>
        <span class="inline-flex rounded-full border border-border bg-surface px-3 py-1.5 text-xs font-medium text-text-secondary">{{ $suppliers->count() }} pemasok terpilih</span>
    </div>
    @if($suppliers->isNotEmpty())
        <div x-data="{ moved: false }">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-border bg-surface/60 px-5 py-3">
                <span class="inline-flex items-center gap-1.5 text-xs text-text-muted"><x-heroicon-o-table-cells class="h-4 w-4 text-brand-700" />Bandingkan sebelum menghubungi</span>
                <span x-show="!moved" class="nb-scroll-hint inline-flex items-center gap-1 text-xs font-medium text-brand-700">Geser tabel<x-heroicon-o-arrow-right class="h-4 w-4" /></span>
            </div>
            <div class="relative overflow-x-auto overscroll-x-contain snap-x" tabindex="0" role="region" aria-label="Tabel pratinjau perbandingan, dapat digeser" @scroll.passive="moved = true">
                <table class="nb-comparison">
                    <caption class="sr-only">Data pemasok dari katalog Namabiz</caption>
                    <thead><tr><th scope="col">Parameter</th>@foreach($suppliers as $supplier)<th scope="col" class="snap-start">{{ $supplier->name }}</th>@endforeach</tr></thead>
                    <tbody>
                        @foreach(['Kapasitas produksi', 'MOQ', 'Lead time', 'Status profil'] as $parameter)
                            <tr>
                                <th scope="row" class="text-xs font-medium">
                                    @if($parameter === 'MOQ')<abbr title="Jumlah pesanan minimum">MOQ</abbr>
                                    @elseif($parameter === 'Lead time')<abbr title="Waktu yang dibutuhkan untuk produksi">Lead time</abbr>
                                    @else{{ $parameter }}@endif
                                </th>
                                @foreach($suppliers as $supplier)
                                    @php($bestMoq = $parameter === 'MOQ' && $lowestMoq !== null && \App\Support\SupplierFormat::quantity($supplier->moq) === $lowestMoq)
                                    <td class="snap-start {{ $bestMoq ? 'bg-brand-50/70 text-brand-700 font-semibold' : '' }}">
                                        @switch($parameter)
                                            @case('Kapasitas produksi') {{ \App\Support\SupplierFormat::capacity($supplier->production_capacity) }} @break
                                            @case('MOQ') {{ \App\Support\SupplierFormat::moq($supplier->moq) }} @break
                                            @case('Lead time') {{ \App\Support\SupplierFormat::leadTime($supplier->avg_lead_time) }} @break
                                            @default
                                                @if($supplier->is_verified)<x-ui.verified />@else<span class="text-amber-700 inline-flex items-center gap-1"><x-heroicon-o-clock class="w-4 h-4 shrink-0" />Belum terverifikasi</span>@endif
                                        @endswitch
                                        @if($bestMoq)<span class="mt-2 inline-flex items-center gap-1 text-xs text-brand-700" title="MOQ terendah dari pemasok dalam pratinjau"><x-heroicon-o-star class="h-4 w-4 shrink-0" />Terbaik<span class="sr-only">: MOQ terendah dalam pratinjau</span></span>@endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <p class="p-6 text-sm text-text-muted">Katalog sedang disiapkan. Data pemasok akan tampil di sini setelah tersedia.</p>
    @endif
    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-border bg-surface px-4 py-3">
        <span class="inline-flex items-center gap-1.5 text-xs text-text-muted"><x-heroicon-o-circle-stack class="h-4 w-4" />Data berasal dari katalog</span>
    </div>
</section>
