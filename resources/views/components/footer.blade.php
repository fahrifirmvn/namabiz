<footer class="bg-white border-t border-border mt-auto" id="kontak">
    <div class="nb-container py-12">
        <div class="grid gap-8 md:grid-cols-4">
            <div>
                <a href="{{ route('home') }}" class="inline-flex min-h-[44px] items-center" aria-label="Namabiz — beranda"><x-application-logo /></a>
                <p class="mt-3 text-sm text-text-muted">Direktori B2B Indonesia</p>
                <p class="mt-2 text-sm text-text-secondary">Kenali pemasok Anda.<br>Bangun kerja sama yang tepat.</p>
            </div>
            @php
                $groups = ['Produk' => ['Jelajahi' => route('discover.index'), 'Cara kerja' => route('home').'#cara-kerja', 'Paket' => route('home').'#paket']];
                foreach (config('namabiz.pages') as $key => $page) {
                    $groups[$page['group']][$page['title']] = \App\Support\PortalContent::pageUrl($key);
                }
            @endphp
            @foreach($groups as $group => $links)
                <div x-data="{ expanded: false }">
                    <h2><button type="button" class="flex min-h-[48px] w-full items-center justify-between text-sm font-semibold md:pointer-events-none"
                                @click="expanded = !expanded" :aria-expanded="expanded || window.innerWidth >= 768" aria-controls="footer-group-{{ $loop->index }}">
                        {{ $group }}<x-heroicon-o-chevron-down class="h-4 w-4 md:hidden transition-transform" ::class="expanded ? 'rotate-180' : ''" />
                    </button></h2>
                    <ul id="footer-group-{{ $loop->index }}" :class="expanded ? 'block' : 'hidden md:block'" class="text-sm text-text-muted">
                        @foreach($links as $label => $href)
                            <li><a class="inline-flex min-h-[44px] items-center hover:text-brand-700" href="{{ $href }}">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
        <x-portal-contacts class="mt-8 border-t border-border pt-6 md:grid-cols-2" />
        <p class="mt-8 text-13 text-text-muted" style="padding-bottom: env(safe-area-inset-bottom)">&copy; {{ now()->year }} Namabiz. Hak cipta dilindungi.</p>
    </div>
</footer>
