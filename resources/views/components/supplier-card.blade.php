@props(['supplier', 'selectable' => false, 'selected' => false, 'limitReached' => false])
@php
    $totalDocsCount = $supplier->documents_count ?? $supplier->documents->count();
    $verifiedCert = $supplier->certifications->firstWhere('status', 'verified')?->certification?->name;
@endphp
<article data-reveal {{ $attributes->merge(['class' => 'group nb-supplier-card nb-card-interactive relative min-w-0 p-5 md:p-6 flex h-full flex-col '.($selected ? 'nb-card-selected !border-brand-700 ring-1 ring-brand-700' : '')]) }}>
    @if($supplier->cover_image)
        <div class="-mx-5 -mt-5 md:-mx-6 md:-mt-6 mb-4 h-32 overflow-hidden rounded-t-2xl border-b border-border bg-slate-100">
            <img src="{{ str_starts_with($supplier->cover_image, 'http') ? $supplier->cover_image : asset('storage/' . $supplier->cover_image) }}"
                 alt="Fasilitas {{ $supplier->name }}"
                 class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
                 loading="lazy" />
        </div>
    @endif
    <div class="flex items-start gap-3">
        <x-ui.avatar :name="$supplier->name" :logo="$supplier->logo" class="!w-11 !h-11" />
        <div class="min-w-0 flex-1">
            <h3 class="text-base font-semibold leading-6 break-words"><a href="{{ route('suppliers.show', $supplier->slug) }}" class="after:absolute after:inset-0 after:rounded-2xl">{{ $supplier->name }}</a></h3>
            @if($supplier->is_verified)<x-ui.verified class="mt-1" />@endif
        </div>
        <div class="relative z-10">
            @auth
                <form method="POST" action="{{ route('suppliers.save', $supplier) }}">@csrf<button type="submit" aria-label="Simpan {{ $supplier->name }}" class="nb-icon-button"><x-heroicon-o-bookmark class="h-5 w-5" /></button></form>
            @else
                <a href="{{ route('login') }}" aria-label="Masuk untuk menyimpan {{ $supplier->name }}" class="nb-icon-button"><x-heroicon-o-bookmark class="h-5 w-5" /></a>
            @endauth
        </div>
    </div>
    <p class="mt-4 text-13 font-medium leading-5 text-text-secondary">{{ \App\Support\SupplierFormat::category($supplier->industry_category ?? '') }}</p>
    <p class="mt-1 flex items-start gap-1.5 text-13 leading-5 text-text-muted"><x-heroicon-o-map-pin class="mt-0.5 h-4 w-4 shrink-0" />{{ $supplier->city }}{{ $supplier->city ? ', ' : '' }}{{ $supplier->country }}</p>
    <div class="nb-scroll-row relative z-10 mt-4">
        @foreach($supplier->tags->take(3) as $tag)<x-ui.chip>{{ \App\Support\SupplierFormat::tag($tag->tag_name) }}</x-ui.chip>@endforeach
        @if($supplier->tags->count() > 3)<span class="nb-chip">+{{ $supplier->tags->count() - 3 }}</span>@endif
    </div>
    <dl class="nb-metric-panel mt-3 grid grid-cols-2 gap-4 rounded-xl p-4 tabular-nums">
        <x-ui.metric label="Kapasitas" :value="\App\Support\SupplierFormat::capacity($supplier->production_capacity)" />
        <x-ui.metric label="MOQ" :value="\App\Support\SupplierFormat::moq($supplier->moq)" />
        <x-ui.metric label="Lead time" :value="\App\Support\SupplierFormat::leadTime($supplier->avg_lead_time)" />
        <x-ui.metric label="Sertifikasi" :value="$verifiedCert ?? 'Belum tervalidasi'" />
    </dl>
    <p class="mt-4 mb-5 text-13 leading-5 text-text-muted">{{ $totalDocsCount }} dokumen tersedia @if($supplier->average_rating) · ★ {{ \App\Support\SupplierFormat::number($supplier->average_rating, 1) }}@endif</p>
    <div class="relative z-10 mt-auto space-y-2">
        <div class="flex gap-2">
            <a class="nb-primary flex-1" href="{{ route('suppliers.show', $supplier->slug) }}">Lihat profil<x-heroicon-o-arrow-right class="h-4 w-4" /></a>
            @if($supplier->whatsapp_url)
                <a href="{{ $supplier->whatsapp_url }}" target="_blank" rel="noopener noreferrer" class="nb-secondary !px-3 !border-brand-700 !text-brand-700" aria-label="Hubungi {{ $supplier->name }} melalui WhatsApp"><x-heroicon-o-chat-bubble-left-right class="h-5 w-5" /><span class="sr-only">WhatsApp</span></a>
            @elseif($supplier->email)
                <a href="mailto:{{ $supplier->email }}" class="nb-secondary !px-3" aria-label="Kirim email ke {{ $supplier->name }}"><x-heroicon-o-envelope class="h-5 w-5" /></a>
            @endif
        </div>
        @if($selectable)<x-ui.compare-toggle :supplier-id="$supplier->id" :supplier-name="$supplier->name" :selected="$selected" :limit-reached="$limitReached" />@endif
    </div>
</article>
