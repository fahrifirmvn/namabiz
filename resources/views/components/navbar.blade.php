<nav x-data="{ open: false }" class="sticky top-0 z-40 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 gap-4">
            <!-- Brand & Main Nav -->
            <div class="flex items-center gap-8 min-w-0">
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black rounded-lg">
                    <x-application-logo />
                </a>

                <div class="hidden md:flex items-center gap-6">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="text-sm font-medium pb-1 transition {{ request()->routeIs('dashboard') ? 'border-b-2 border-black font-semibold text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                            Beranda
                        </a>
                        <a href="{{ route('discover.index') }}"
                           class="text-sm font-medium pb-1 transition {{ request()->routeIs('discover.*') ? 'border-b-2 border-black font-semibold text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                            Jelajahi
                        </a>
                        <a href="{{ route('compare.index') }}"
                           class="text-sm font-medium pb-1 transition {{ request()->routeIs('compare.*') ? 'border-b-2 border-black font-semibold text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                            Bandingkan
                        </a>
                    @else
                        <a href="{{ route('discover.index') }}"
                           class="text-sm font-medium pb-1 transition {{ request()->routeIs('discover.*') ? 'border-b-2 border-black font-semibold text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                            Jelajahi
                        </a>
                        <a href="{{ url('/#cara-kerja') }}"
                           class="text-sm font-medium pb-1 text-gray-600 hover:text-gray-900 transition">
                            Cara kerja
                        </a>
                        <a href="{{ url('/#paket') }}"
                           class="text-sm font-medium pb-1 text-gray-600 hover:text-gray-900 transition">
                            Paket
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Global Search Bar with '/' Shortcut -->
            <form method="GET" action="{{ route('discover.index') }}"
                  class="hidden lg:flex flex-1 max-w-md mx-2"
                  x-data="{
                      init() {
                          window.addEventListener('keydown', (e) => {
                              if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                                  e.preventDefault();
                                  this.$refs.searchInput.focus();
                              }
                          });
                      }
                  }">
                <div class="relative w-full">
                    <x-heroicon-o-magnifying-glass aria-hidden="true" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <label for="navbar-search" class="sr-only">Cari pemasok atau bahan baku</label>
                    <input id="navbar-search"
                           x-ref="searchInput"
                           name="q"
                           type="search"
                           value="{{ request('q', request('query')) }}"
                           placeholder="Cari pemasok atau bahan baku"
                           class="w-full rounded-lg bg-gray-50 border border-gray-200 pl-10 pr-9 py-2 text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-black focus:border-black transition">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                        <kbd class="px-1.5 py-0.5 text-xs text-gray-400 bg-white border border-gray-200 rounded font-mono shadow-2xs">/</kbd>
                    </div>
                </div>
            </form>

            <!-- Actions / Auth Area -->
            <div class="flex items-center gap-3 shrink-0">
                @auth
                    <!-- Subscription Tier Indicator -->
                    <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ auth()->user()->isPremium() ? 'bg-amber-100 text-amber-900' : 'bg-gray-100 text-gray-600' }}">
                        {{ auth()->user()->isPremium() ? 'Premium' : 'Paket Gratis' }}
                    </span>

                    @unless (auth()->user()->isPremium())
                        <a href="{{ route('upgrade') }}" class="hidden sm:inline-flex items-center gap-1.5 bg-black text-white rounded-lg px-3.5 py-2 text-sm font-medium hover:bg-gray-800 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black">
                            <x-heroicon-s-sparkles class="w-4 h-4 text-amber-300" />
                            Upgrade
                        </a>
                    @endunless

                    <!-- Notification Bell (Static / Mockup) -->
                    <button type="button" aria-label="Notifikasi" class="relative p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition focus-visible:ring-2 focus-visible:ring-black">
                        <x-heroicon-o-bell class="w-5 h-5" />
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-emerald-500 rounded-full"></span>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ profileOpen: false }">
                        <button type="button" @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                                :aria-expanded="profileOpen" aria-controls="profile-menu" aria-label="Menu profil pengguna"
                                class="w-9 h-9 rounded-full bg-black text-white text-xs font-semibold flex items-center justify-center overflow-hidden focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black shadow-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </button>
                        <div id="profile-menu" x-show="profileOpen" x-cloak
                             class="absolute right-0 mt-2 w-52 bg-white rounded-xl border border-gray-200 shadow-xl py-1 text-sm divide-y divide-gray-100">
                            <div class="px-4 py-2.5">
                                <p class="font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Profil Saya</a>
                                <a href="{{ route('upgrade') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Langganan & Billing</a>
                                @can('access-admin')
                                    <a href="{{ route('admin.suppliers.index') }}" class="block px-4 py-2 font-medium text-emerald-700 hover:bg-emerald-50">Admin Portal</a>
                                @endcan
                            </div>
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900 px-3 py-2 rounded-lg transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="bg-black text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-gray-800 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black">
                        Daftar gratis
                    </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="mobile-navigation"
                        aria-label="Menu navigasi mobile"
                        class="inline-flex md:hidden items-center justify-center rounded-lg p-2 text-gray-600 hover:bg-gray-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black">
                    <svg x-show="!open" aria-hidden="true" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" aria-hidden="true" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M6 6l12 12M6 18L18 6" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div id="mobile-navigation" x-show="open" x-cloak class="md:hidden border-t border-gray-100 py-4">
            <form method="GET" action="{{ route('discover.index') }}" class="mb-4">
                <label for="mobile-navbar-search" class="sr-only">Cari pemasok atau bahan baku</label>
                <div class="relative">
                    <x-heroicon-o-magnifying-glass aria-hidden="true" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                    <input id="mobile-navbar-search" name="q" type="search" value="{{ request('q', request('query')) }}" placeholder="Cari pemasok atau bahan baku..."
                           class="w-full rounded-lg bg-gray-50 border border-gray-200 pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-black">
                </div>
            </form>

            <div class="space-y-1 text-sm font-medium">
                @auth
                    <a href="{{ route('dashboard') }}" @click="open = false"
                       class="block rounded-lg px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">Beranda</a>
                    <a href="{{ route('discover.index') }}" @click="open = false"
                       class="block rounded-lg px-3 py-2 {{ request()->routeIs('discover.*') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">Jelajahi</a>
                    <a href="{{ route('compare.index') }}" @click="open = false"
                       class="block rounded-lg px-3 py-2 {{ request()->routeIs('compare.*') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">Bandingkan</a>
                    @unless (auth()->user()->isPremium())
                        <a href="{{ route('upgrade') }}" @click="open = false" class="block rounded-lg px-3 py-2 text-amber-700 bg-amber-50">Upgrade ke Premium</a>
                    @endunless
                    @can('access-admin')
                        <a href="{{ route('admin.suppliers.index') }}" @click="open = false" class="block rounded-lg px-3 py-2 text-emerald-700 hover:bg-gray-50">Admin Portal</a>
                    @endcan
                    <a href="{{ route('profile') }}" @click="open = false" class="block rounded-lg px-3 py-2 text-gray-600 hover:bg-gray-50">Profil Saya</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left rounded-lg px-3 py-2 text-gray-600 hover:bg-gray-50">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('discover.index') }}" @click="open = false"
                       class="block rounded-lg px-3 py-2 {{ request()->routeIs('discover.*') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">Jelajahi</a>
                    <a href="{{ url('/#cara-kerja') }}" @click="open = false" class="block rounded-lg px-3 py-2 text-gray-600 hover:bg-gray-50">Cara kerja</a>
                    <a href="{{ url('/#paket') }}" @click="open = false" class="block rounded-lg px-3 py-2 text-gray-600 hover:bg-gray-50">Paket</a>
                    <div class="pt-2 border-t border-gray-100 flex gap-2">
                        <a href="{{ route('login') }}" class="flex-1 text-center py-2 border border-gray-200 rounded-lg text-gray-700">Masuk</a>
                        <a href="{{ route('register') }}" class="flex-1 text-center py-2 bg-black text-white rounded-lg">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
