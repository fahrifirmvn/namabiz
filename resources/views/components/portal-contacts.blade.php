@php
    $contacts = \App\Support\PortalContent::contacts();
    $hasContacts = $contacts['email'] || $contacts['address'] || $contacts['whatsapp'] || $contacts['social'];
@endphp
@if($hasContacts)
    <address {{ $attributes->merge(['class' => 'grid gap-3 text-sm text-text-muted not-italic']) }}>
        @if($contacts['whatsapp'])<a href="{{ $contacts['whatsapp'] }}" target="_blank" rel="noopener noreferrer" class="nb-accent justify-self-start">Hubungi kurator via WhatsApp<x-heroicon-o-arrow-up-right class="h-4 w-4" /></a>@endif
        @if($contacts['email'])<a href="mailto:{{ $contacts['email'] }}" class="inline-flex min-h-[44px] items-center gap-2 break-all hover:text-brand-700"><x-heroicon-o-envelope class="h-5 w-5 shrink-0" />{{ $contacts['email'] }}</a>@endif
        @if($contacts['address'])<p class="flex items-start gap-2 leading-6"><x-heroicon-o-map-pin class="mt-0.5 h-5 w-5 shrink-0" />{{ $contacts['address'] }}</p>@endif
        @foreach($contacts['social'] as $label => $url)<a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-[44px] items-center gap-2 hover:text-brand-700">{{ $label }}<x-heroicon-o-arrow-up-right class="h-4 w-4" /></a>@endforeach
    </address>
@endif
