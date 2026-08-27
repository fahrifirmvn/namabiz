<div x-data x-show="$wire.show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-black/50" wire:click="close"></div>
    <div class="relative bg-white rounded-2xl p-8 w-full max-w-md text-center shadow-xl">
        <div class="w-16 h-16 bg-gray-100 rounded-xl mx-auto flex items-center justify-center mb-4">
            <x-heroicon-o-lock-closed class="w-7 h-7 text-gray-700" />
        </div>
        <h2 class="text-2xl font-bold">Premium Document</h2>
        <p class="text-gray-500 mt-2">This {{ $documentTitle }} is available to Premium members. Get access to the complete supplier document library and more.</p>
        <a href="{{ route('upgrade') }}" class="block bg-black text-white rounded-lg py-3 mt-6 font-medium hover:bg-gray-800">Unlock with Premium</a>
        <button wire:click="close" class="text-gray-500 mt-4 text-sm">Continue browsing</button>
    </div>
</div>
