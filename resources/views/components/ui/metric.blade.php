@props([
    'label',
    'value',
])

<div {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <dt class="block text-xs uppercase tracking-wider font-medium text-text-muted">
        {{ $label }}
    </dt>
    <dd class="block text-sm font-semibold text-text-primary mt-1" title="{{ $value }}">
        {{ $value }}
    </dd>
</div>
