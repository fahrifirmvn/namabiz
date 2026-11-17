@props(['selectedSuppliers' => collect(), 'maxSuppliers' => 4])
@if(count($selectedSuppliers) > 0)
    <div class="nb-compare-tray fixed inset-x-0 z-40 bg-slate-900 text-white shadow-tray" role="region" aria-label="Pemasok untuk dibandingkan" x-data="{ limitNotice: false }" @compare-limit-reached.window="limitNotice = true">
        <div x-show="limitNotice" x-cloak class="nb-container flex items-center justify-between gap-3 py-2 text-sm text-amber-200" role="status">Maksimal {{ $maxSuppliers }} pemasok. Hapus salah satu pilihan untuk mengganti.<button type="button" class="nb-icon-button !text-white" @click="limitNotice = false" aria-label="Tutup pemberitahuan"><x-heroicon-o-x-mark class="h-5 w-5" /></button></div>
        <div class="nb-container flex items-center justify-between gap-3 py-3">
            <div class="min-w-0">
                <p class="text-sm font-semibold" role="status"><span wire:key="tray-count-{{ count($selectedSuppliers) }}" class="nb-count-pop inline-block">{{ count($selectedSuppliers) }} dipilih</span> <span class="text-xs font-normal text-slate-300">(maks {{ $maxSuppliers }})</span></p>
                <div class="hidden md:flex gap-2 mt-2">
                    @foreach($selectedSuppliers as $selected)<button type="button" wire:click="toggleCompare({{ $selected->id }})" class="inline-flex min-h-[44px] items-center gap-2 rounded-xl bg-slate-800 px-3 text-xs" aria-label="Hapus {{ $selected->name }} dari perbandingan">{{ $selected->name }}<x-heroicon-o-x-mark class="h-4 w-4" /></button>@endforeach
                </div>
                <button type="button" wire:click="clearCompare" class="min-h-[44px] text-xs text-slate-300 underline underline-offset-4">Hapus semua</button>
            </div>
            <button type="button" wire:click="goToCompare" class="nb-accent !px-4 shrink-0" wire:loading.attr="disabled" wire:target="goToCompare">Bandingkan<x-heroicon-o-arrow-right class="h-4 w-4" /></button>
        </div>
    </div>
@endif
