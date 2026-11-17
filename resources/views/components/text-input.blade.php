@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'h-[52px] px-4 border-border text-text-primary placeholder-text-muted focus:border-brand-700 focus:ring-2 focus:ring-brand-600/20 rounded-xl text-base transition duration-180 disabled:bg-surface disabled:cursor-not-allowed']) }}>
