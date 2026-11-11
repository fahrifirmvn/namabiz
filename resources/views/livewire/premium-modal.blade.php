<div x-data x-show="$wire.show" x-cloak x-on:keydown.escape.window="$wire.close()"
     role="dialog" aria-modal="true" aria-labelledby="premium-modal-title"
     class="fixed inset-0 z-50 flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-black/50" wire:click="close" aria-hidden="true"></div>
    <div tabindex="-1" class="relative bg-white rounded-2xl p-8 w-full max-w-md text-center shadow-xl">
        <div class="w-16 h-16 bg-gray-100 rounded-xl mx-auto flex items-center justify-center mb-4">
            <x-heroicon-o-lock-closed aria-hidden="true" class="w-7 h-7 text-gray-700" />
        </div>
        <h2 id="premium-modal-title" class="text-2xl font-bold text-gray-900">Dokumen Khusus Premium</h2>
        <p class="text-sm text-gray-600 mt-2">Dokumen <strong class="text-gray-900">{{ $documentTitle }}</strong> hanya tersedia untuk anggota Premium. Buka akses lengkap pustaka dokumen pemasok dan metrik analitis.</p>
        <a href="{{ route('upgrade') }}" class="block bg-black text-white rounded-xl py-3 mt-6 text-sm font-semibold hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2 transition">Buka dengan Paket Premium</a>
        <button type="button" wire:click="close" class="text-gray-600 mt-4 text-xs underline underline-offset-2 hover:text-gray-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black rounded">Lanjutkan menelusuri</button>
    </div>
</div>
