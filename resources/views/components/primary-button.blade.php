<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-black border border-transparent rounded-xl font-semibold text-xs text-white tracking-wide hover:bg-gray-800 focus:bg-gray-800 active:bg-black focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 disabled:opacity-60 transition ease-in-out duration-150 shadow-xs']) }}>
    {{ $slot }}
</button>
