@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'h-11 sm:h-12 px-3.5 border-gray-300 text-text-primary placeholder-text-muted focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 rounded-xl shadow-2xs text-sm transition duration-180']) }}>
