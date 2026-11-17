@props([
    'name' => '',
    'logo' => null,
    'size' => 'md',
])

@php
    $initial = strtoupper(mb_substr(trim((string) $name), 0, 2)) ?: 'NB';
    $hasValidLogo = $logo && !str_contains((string) $logo, 'placeholder');
    $paletteClass = $hasValidLogo
        ? 'bg-white border-slate-200 p-1'
        : 'bg-slate-100 text-slate-800 border-slate-200';

    $sizeClass = match ($size) {
        'sm' => 'w-10 h-10 text-xs',
        'lg' => 'w-14 h-14 text-base',
        default => 'w-12 h-12 text-sm',
    };
@endphp

<div {{ $attributes->merge(['class' => "$sizeClass rounded-xl border font-semibold flex items-center justify-center shrink-0 overflow-hidden select-none $paletteClass"]) }}>
    @if($hasValidLogo)
        <img src="{{ str_starts_with((string) $logo, 'http') ? $logo : asset('storage/' . $logo) }}"
             alt="{{ $name }}"
             class="w-full h-full object-contain"
             loading="lazy" width="48" height="48" />
    @else
        <span>{{ $initial }}</span>
    @endif
</div>
