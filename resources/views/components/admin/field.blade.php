@props([
    'label',
    'name',
    'value' => null,
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'hint' => null,
])

<div {{ $attributes }}>
    <label for="{{ $name }}" class="block text-xs font-bold text-gray-700 mb-1">
        {{ $label }}@if($required) <span class="text-red-500">*</span>@endif
    </label>
    <input id="{{ $name }}"
           type="{{ $type }}"
           name="{{ $name }}"
           value="{{ old($name, $value) }}"
           placeholder="{{ $placeholder }}"
           @required($required)
           class="w-full text-sm rounded-xl shadow-xs focus:border-black focus:ring-black {{ $errors->has($name) ? 'border-red-400' : 'border-gray-300' }}" />
    @if($hint)
        <p class="text-2xs text-gray-400 mt-1">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="text-2xs text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
