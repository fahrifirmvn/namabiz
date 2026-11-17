<nav aria-label="Navigasi akun" class="nb-bottom-nav fixed inset-x-0 bottom-0 z-40 bg-white border-t border-border md:hidden"
     x-data="{ count: {{ count(session('compared_suppliers', [])) }} }" @compare-updated.window="count = $event.detail.count">
    <div class="grid h-14 grid-cols-4">
        @foreach([
            ['route' => 'discover.index', 'icon' => 'magnifying-glass', 'label' => 'Jelajahi'],
            ['route' => 'saved.index', 'icon' => 'bookmark', 'label' => 'Tersimpan'],
            ['route' => 'compare.index', 'icon' => 'scale', 'label' => 'Bandingkan'],
            ['route' => 'profile', 'icon' => 'user-circle', 'label' => 'Akun'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="relative flex flex-col items-center justify-center gap-1 text-xs {{ request()->routeIs($item['route']) ? 'text-brand-700 font-semibold bg-brand-50' : 'text-text-secondary' }}" @if(request()->routeIs($item['route'])) aria-current="page" @endif>
                <x-dynamic-component :component="'heroicon-o-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
                @if($item['route'] === 'compare.index')<span x-show="count > 0" x-cloak x-text="count" class="absolute right-3 top-1 rounded-full bg-brand-700 px-1.5 text-xs text-white" aria-label="Jumlah pemasok dipilih"></span>@endif
            </a>
        @endforeach
    </div>
</nav>
