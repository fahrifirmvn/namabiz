@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-xs text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>
