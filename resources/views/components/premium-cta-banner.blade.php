@props(['supplierName' => null])

<div class="bg-gray-900 text-white rounded-xl p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 mt-8">
    <div>
        <h3 class="text-xl font-bold flex items-center gap-2">
            <x-heroicon-o-shield-check class="w-5 h-5" />
            Unlock complete {{ $supplierName ? "{$supplierName}'s" : 'supplier' }} data
        </h3>
        <p class="text-gray-300 mt-1">Compare operational metrics, lead times, and download full PDF reports to make informed procurement decisions.</p>
    </div>
    <a href="{{ route('upgrade') }}" class="bg-white text-black rounded-lg px-6 py-3 font-medium whitespace-nowrap hover:bg-gray-100">Explore Premium</a>
</div>
