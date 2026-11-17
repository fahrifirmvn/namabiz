<header x-data="{ panel: null, scrolled: false }" @scroll.window.passive="scrolled = window.scrollY > 8"
        @keydown.escape.window="panel = null" class="nb-global-header sticky top-0 z-40 bg-white/90 backdrop-blur-md"
        :class="scrolled ? 'shadow-sm' : ''">
    <nav class="nb-container flex h-14 md:h-[72px] items-center justify-between gap-4" aria-label="Navigasi utama">
        <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="inline-flex min-h-[44px] items-center rounded-xl" aria-label="Namabiz — beranda"><x-application-logo /></a>
        <div class="hidden md:flex items-center gap-1">
            @auth
                <a class="nb-ghost px-3" href="{{ route('dashboard') }}">Beranda</a>
            @endauth
            <a class="nb-ghost px-3" href="{{ route('discover.index') }}" @if(request()->routeIs('discover.*')) aria-current="page" @endif>Jelajahi</a>
            @guest
                <a class="nb-ghost px-3" href="{{ route('home') }}#cara-kerja">Cara kerja</a>
                <a class="nb-ghost px-3" href="{{ route('home') }}#paket">Paket</a>
            @else
                <a class="nb-ghost px-3" href="{{ route('saved.index') }}">Tersimpan</a>
                <a class="nb-ghost px-3" href="{{ route('compare.index') }}">Bandingkan</a>
            @endguest
        </div>
        @unless(request()->routeIs('home', 'discover.*'))
            <form method="GET" action="{{ route('discover.index') }}" class="hidden xl:flex min-w-0 flex-1 max-w-xs nb-search !h-12">
                <x-heroicon-o-magnifying-glass class="h-5 w-5 ml-2 shrink-0 text-text-muted" aria-hidden="true" />
                <input name="q" type="search" aria-label="Cari pemasok" placeholder="Cari pemasok" value="{{ request('q') }}">
            </form>
        @endunless
        <div class="hidden md:flex items-center gap-2">
            @auth
                <a class="nb-ghost px-3" href="{{ route('profile') }}">Akun</a>
                @can('access-admin')<a class="nb-ghost px-3" href="{{ route('admin.suppliers.index') }}">Admin</a>@endcan
                @unless(auth()->user()->isPremium())<a class="nb-primary" href="{{ route('upgrade') }}">Premium</a>@endunless
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="nb-ghost px-3" type="submit">Keluar</button></form>
            @else
                <a class="nb-ghost" href="{{ route('login') }}">Masuk</a>
                <a class="nb-primary" href="{{ route('register') }}">Daftar gratis</a>
            @endauth
        </div>
        <div class="flex md:hidden">
            <button class="nb-icon-button" type="button" @click="panel = 'search'" aria-label="Buka pencarian" aria-controls="mobile-search" :aria-expanded="panel === 'search'"><x-heroicon-o-magnifying-glass class="h-5 w-5" /></button>
            <button class="nb-icon-button" type="button" @click="panel = 'menu'" aria-label="Buka menu" aria-controls="mobile-menu" :aria-expanded="panel === 'menu'"><x-heroicon-o-bars-3 class="h-5 w-5" /></button>
        </div>
    </nav>
    <div data-scroll-progress class="nb-scroll-progress" style="transform: scaleX(0)" aria-hidden="true"></div>
    <template x-teleport="body">
        <div x-cloak x-show="panel !== null" x-transition:enter="nb-panel-transition" x-transition:enter-start="nb-menu-from" x-transition:enter-end="nb-panel-to"
             x-transition:leave="nb-panel-transition" x-transition:leave-start="nb-panel-to" x-transition:leave-end="nb-menu-from" x-trap.inert.noscroll="panel !== null"
             class="fixed inset-0 z-[70] flex flex-col bg-white p-4 md:hidden" style="padding-top: max(16px, env(safe-area-inset-top)); padding-bottom: max(16px, env(safe-area-inset-bottom))"
             role="dialog" aria-modal="true" :aria-label="panel === 'menu' ? 'Menu navigasi' : 'Pencarian pemasok'">
            <div class="flex items-center justify-between pb-6">
                <x-application-logo />
                <button class="nb-icon-button" type="button" @click="panel = null" aria-label="Tutup panel"><x-heroicon-o-x-mark class="h-5 w-5" /></button>
            </div>
            <div id="mobile-search" x-show="panel === 'search'">
                <h2 class="text-2xl font-bold mb-5">Cari pemasok</h2>
                <form action="{{ route('discover.index') }}" method="GET" class="space-y-4">
                    <label for="overlay-search" class="text-sm font-medium">Nama, produk, atau bahan baku</label>
                    <input id="overlay-search" type="search" name="q" class="h-14 w-full rounded-xl border-border" placeholder="Apa yang Anda butuhkan?">
                    <button class="nb-primary w-full" type="submit">Cari pemasok</button>
                </form>
            </div>
            <div id="mobile-menu" x-show="panel === 'menu'" class="flex flex-1 flex-col">
                <nav aria-label="Menu seluler" class="flex flex-col gap-2 text-lg">
                    <a class="nb-ghost justify-start" href="{{ route('discover.index') }}">Jelajahi</a>
                    <a class="nb-ghost justify-start" href="{{ route('home') }}#cara-kerja" @click="panel = null">Cara kerja</a>
                    <a class="nb-ghost justify-start" href="{{ route('home') }}#paket" @click="panel = null">Paket</a>
                    @auth
                        <a class="nb-ghost justify-start" href="{{ route('dashboard') }}">Beranda</a>
                        <a class="nb-ghost justify-start" href="{{ route('profile') }}">Akun Anda</a>
                        @can('access-admin')<a class="nb-ghost justify-start" href="{{ route('admin.suppliers.index') }}">Admin</a>@endcan
                    @endauth
                </nav>
                <div class="mt-auto grid gap-3 pt-8">
                    @guest
                        <a class="nb-secondary" href="{{ route('login') }}">Masuk</a>
                        <a class="nb-primary" href="{{ route('register') }}">Daftar gratis</a>
                    @else
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="nb-secondary w-full" type="submit">Keluar</button></form>
                    @endguest
                </div>
            </div>
        </div>
    </template>
</header>
