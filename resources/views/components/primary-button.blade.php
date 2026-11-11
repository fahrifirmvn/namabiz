<button {{ $attributes->merge(['type' => 'submit', 'class' => 'h-11 sm:h-12 inline-flex items-center justify-center gap-2 px-5 bg-gray-900 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-black focus:bg-black active:bg-gray-900 nb-focus-ring disabled:opacity-60 disabled:cursor-not-allowed transition duration-180 ease-out shadow-xs']) }}>
    {{ $slot }}
</button>
