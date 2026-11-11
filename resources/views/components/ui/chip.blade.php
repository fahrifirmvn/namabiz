@props([
    'variant' => 'default',
])

@php
    $variantClass = match ($variant) {
        'muted' => 'bg-transparent text-text-secondary border-transparent px-1.5',
        'brand' => 'bg-brand-50 text-brand-800 border-brand-200 px-2.5',
        default => 'bg-surface-subtle text-text-secondary border-border/80 px-2.5',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center h-6 rounded-lg text-xs font-medium border whitespace-nowrap shrink-0 $variantClass"]) }}>
    {{ $slot }}
</span>
