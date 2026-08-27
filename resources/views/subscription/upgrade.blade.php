<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        @if (session('status'))
            <div class="bg-green-50 text-green-700 rounded-lg px-4 py-3 mb-8 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if (session('paywall_triggered'))
            <div class="bg-amber-50 text-amber-700 rounded-lg px-4 py-3 mb-8 text-sm">
                That feature requires a Premium subscription.
            </div>
        @endif

        <div class="w-16 h-16 bg-gray-900 rounded-2xl mx-auto flex items-center justify-center mb-6">
            <x-heroicon-o-shield-check class="w-8 h-8 text-white" />
        </div>

        <h1 class="text-3xl font-bold text-text-primary">Upgrade to Premium</h1>
        <p class="text-text-secondary mt-2">Unlock operational metrics, full certifications, institutional documents, and PDF comparison exports.</p>

        <div class="bg-white border border-gray-200 rounded-xl p-8 mt-8 text-left">
            <ul class="space-y-3 text-sm text-gray-700">
                <li class="flex items-center gap-2"><x-heroicon-o-check class="w-4 h-4 text-green-500" /> Full operational metrics (capacity, MOQ, lead time)</li>
                <li class="flex items-center gap-2"><x-heroicon-o-check class="w-4 h-4 text-green-500" /> Complete certification &amp; compliance data</li>
                <li class="flex items-center gap-2"><x-heroicon-o-check class="w-4 h-4 text-green-500" /> Unlimited institutional document downloads</li>
                <li class="flex items-center gap-2"><x-heroicon-o-check class="w-4 h-4 text-green-500" /> Download supplier comparisons as PDF</li>
            </ul>

            @if(auth()->user()?->isPremium())
                <p class="mt-6 text-sm text-text-secondary">You're already a Premium member. Thank you for your support!</p>
            @else
                <form method="POST" action="{{ route('upgrade.checkout') }}" class="mt-6">
                    @csrf
                    <button type="submit" class="w-full bg-black text-white rounded-lg py-3 font-medium hover:bg-gray-800">
                        Upgrade Now
                    </button>
                    <p class="text-xs text-text-secondary text-center mt-2">Demo checkout — no payment required yet.</p>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
