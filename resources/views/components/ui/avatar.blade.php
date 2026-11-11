@props([
    'name' => '',
    'logo' => null,
    'size' => 'md',
])

@php
    $initial = strtoupper(mb_substr(trim((string) $name), 0, 2)) ?: 'NB';
    $palettes = [
        'bg-emerald-50 text-emerald-800 border-emerald-200/80',
        'bg-sky-50 text-sky-800 border-sky-200/80',
        'bg-amber-50 text-amber-900 border-amber-200/80',
        'bg-violet-50 text-violet-800 border-violet-200/80',
        'bg-rose-50 text-rose-800 border-rose-200/80',
        'bg-slate-100 text-slate-800 border-slate-200/80',
    ];
    $paletteIndex = abs(crc32((string) $name)) % count($palettes);
    $paletteClass = $palettes[$paletteIndex];

    $sizeClass = match ($size) {
        'sm' => 'w-10 h-10 text-xs',
        'lg' => 'w-14 h-14 text-base',
        default => 'w-12 h-12 text-sm',
    };
    $hasValidLogo = $logo && !str_contains((string) $logo, 'placeholder');
@endphp

<div {{ $attributes->merge(['class' => "$sizeClass rounded-xl border font-semibold flex items-center justify-center shrink-0 overflow-hidden select-none $paletteClass"]) }}>
    @if($hasValidLogo)
        <img src="{{ str_starts_with((string) $logo, 'http') ? $logo : asset('storage/' . $logo) }}"
             alt="{{ $name }}"
             class="w-full h-full object-cover"
             loading="lazy" />
    @else
        <span>{{ $initial }}</span>
    @endif
</div>
