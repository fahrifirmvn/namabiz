@props([
    'selectedSuppliers' => collect(),
    'maxSuppliers' => 4,
])

@php
    $count = count($selectedSuppliers);
@endphp

@if($count > 0)
    <div x-data="{ limitNotice: false }"
         @compare-limit-reached.window="limitNotice = true; setTimeout(() => limitNotice = false, 4500)"
         role="region"
         aria-label="Baki perbandingan pemasok"
         class="fixed bottom-0 inset-x-0 z-50 bg-gray-900/95 backdrop-blur-md text-white border-t border-gray-800 shadow-tray transition-all duration-220 ease-out">
        <!-- Friendly Max Limit Feedback Banner -->
        <div x-show="limitNotice"
             x-cloak
             x-transition.opacity
             role="status"
             aria-live="polite"
             class="bg-amber-500/15 border-b border-amber-400/30 text-amber-200 text-xs py-2 px-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
                <span class="inline-flex items-center gap-2 font-medium">
                    <x-heroicon-s-exclamation-triangle class="w-4 h-4 text-amber-400 shrink-0" />
                    <span>Maksimal {{ $maxSuppliers }} pemasok dapat dibandingkan sekaligus. Hapus salah satu pemasok di bawah untuk mengganti pilihan.</span>
                </span>
                <button type="button" @click="limitNotice = false" class="text-amber-200 hover:text-white text-xs font-semibold px-2 py-0.5 rounded">
                    Tutup
                </button>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3.5">
            <div class="flex items-center gap-3 flex-wrap justify-center sm:justify-start">
                <div class="inline-flex items-center gap-2 text-sm font-semibold text-white">
                    <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                    <span>{{ $count }} pemasok dipilih</span>
                    <span class="text-gray-400 font-normal text-xs">(maks. {{ $maxSuppliers }})</span>
                </div>

                <span class="text-gray-600 hidden sm:inline" aria-hidden="true">&middot;</span>

                <div class="flex items-center gap-2 flex-wrap">
                    @foreach($selectedSuppliers as $selected)
                        <span class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1 rounded-lg bg-gray-800 text-xs font-medium text-gray-100 border border-gray-700">
                            <span class="truncate max-w-[140px]">{{ $selected->name }}</span>
                            <button type="button"
                                    wire:click="toggleCompare({{ $selected->id }})"
                                    aria-label="Hapus {{ $selected->name }} dari perbandingan"
                                    class="p-0.5 text-gray-400 hover:text-white rounded nb-focus-ring">
                                <x-heroicon-m-x-mark class="w-3.5 h-3.5" />
                            </button>
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-4 shrink-0 w-full sm:w-auto justify-between sm:justify-end">
                <button type="button"
                        wire:click="clearCompare"
                        class="min-h-[40px] px-2 text-13 font-medium text-gray-300 hover:text-white underline underline-offset-4 transition nb-focus-ring rounded-lg">
                    Hapus semua
                </button>
                <button type="button"
                        wire:click="goToCompare"
                        class="min-h-[40px] px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-13 transition inline-flex items-center justify-center gap-2 shadow-xs nb-focus-ring">
                    <span>Lihat perbandingan ({{ $count }})</span>
                    <x-heroicon-m-arrow-right class="w-4 h-4" />
                </button>
            </div>
        </div>
    </div>
@endif
