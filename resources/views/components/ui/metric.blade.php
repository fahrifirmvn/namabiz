@props([
    'label',
    'value',
])

<div {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <span class="block text-xs uppercase tracking-wider font-medium text-text-muted truncate">
        {{ $label }}
    </span>
    <span class="block text-sm font-medium text-text-primary mt-0.5 truncate" title="{{ $value }}">
        {{ $value }}
    </span>
</div>
