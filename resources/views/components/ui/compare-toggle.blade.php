@props([
    'supplierId',
    'supplierName' => '',
    'selected' => false,
    'limitReached' => false,
])

<button type="button"
        @if($limitReached && !$selected)
            @click="$dispatch('compare-limit-reached')"
        @else
            wire:click="toggleCompare({{ $supplierId }})"
        @endif
        aria-pressed="{{ $selected ? 'true' : 'false' }}"
        aria-label="{{ $selected ? 'Hapus ' . $supplierName . ' dari perbandingan' : 'Tambah ' . $supplierName . ' ke perbandingan' }}"
        {{ $attributes->merge([
            'class' => 'w-full min-h-[40px] px-3.5 py-2 rounded-xl text-13 font-medium inline-flex items-center justify-center gap-1.5 transition duration-180 ease-out nb-focus-ring ' . (
                $selected
                    ? 'bg-brand-50 text-brand-800 border border-brand-200 hover:bg-brand-100/80'
                    : 'bg-surface hover:bg-surface-subtle text-text-secondary hover:text-text-primary border border-transparent hover:border-border'
            )
        ]) }}>
    @if($selected)
        <x-heroicon-m-check class="w-4 h-4 text-brand-600 shrink-0" />
        <span>&#10003; Ditambahkan</span>
    @else
        <x-heroicon-m-plus class="w-4 h-4 text-text-muted shrink-0" />
        <span>+ Tambah ke perbandingan</span>
    @endif
</button>
