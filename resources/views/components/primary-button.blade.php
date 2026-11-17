<button {{ $attributes->merge(['type' => 'submit', 'class' => 'nb-primary h-[52px]']) }}>
    {{ $slot }}
</button>
