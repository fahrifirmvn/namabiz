<footer class="bg-gray-100 border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-text-secondary">
        <span>&copy; {{ now()->year }} SupplyChain Pro. Industrial Data Systems.</span>
        <nav aria-label="Footer navigation" class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2">
            <a href="{{ route('discover.index') }}" class="hover:text-text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black rounded">Discover</a>
            <a href="{{ route('compare.index') }}" class="hover:text-text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black rounded">Analytics</a>
        </nav>
    </div>
</footer>
