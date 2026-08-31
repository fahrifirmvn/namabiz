<nav x-data="{ open: false }" class="sticky top-0 z-40 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center min-h-16 gap-3">
            <div class="flex items-center gap-8 min-w-0">
                <a href="{{ route('discover.index') }}" class="font-bold text-xl text-text-primary shrink-0">
                    SupplyChain Pro
                </a>

                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('discover.index') }}"
                       class="text-sm font-medium pb-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black {{ request()->routeIs('discover.*') ? 'border-b-2 border-black font-semibold text-text-primary' : 'text-text-secondary' }}">
                        Discover
                    </a>
                    <a href="{{ route('compare.index') }}"
                       class="text-sm font-medium pb-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black {{ request()->routeIs('compare.*') ? 'border-b-2 border-black font-semibold text-text-primary' : 'text-text-secondary' }}">
                        Analytics
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('discover.index') }}" class="hidden lg:flex flex-1 max-w-md">
                <div class="relative w-full">
                    <x-heroicon-o-magnifying-glass aria-hidden="true" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                    <label for="navbar-search" class="sr-only">Search suppliers and materials</label>
                    <input id="navbar-search" name="query" type="search" value="{{ request('query') }}" placeholder="Search suppliers, materials..."
                           class="w-full rounded-lg bg-gray-100 border-none pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-black">
                </div>
            </form>

            <div class="flex items-center gap-3 shrink-0">
                @unless (auth()->user()?->isPremium())
                    <a href="{{ route('upgrade') }}" class="hidden sm:inline-flex bg-black text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2">
                        Upgrade
                    </a>
                @endunless

                @auth
                    <div class="relative hidden md:block" x-data="{ profileOpen: false }">
                        <button type="button" @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                                :aria-expanded="profileOpen" aria-controls="profile-menu" aria-label="Open profile menu"
                                class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black">
                            <x-heroicon-o-user aria-hidden="true" class="w-5 h-5 text-gray-500" />
                        </button>
                        <div id="profile-menu" x-show="profileOpen" x-cloak
                             class="absolute right-0 mt-2 w-44 bg-white rounded-lg border border-gray-200 shadow-lg py-1 text-sm">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 hover:bg-gray-50 focus-visible:bg-gray-50">Profile</a>
                            <a href="{{ route('upgrade') }}" class="block px-4 py-2 hover:bg-gray-50 focus-visible:bg-gray-50">Billing</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-50 focus-visible:bg-gray-50">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden md:inline text-sm font-medium text-text-secondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black">Login</a>
                @endauth

                <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="mobile-navigation"
                        aria-label="Open navigation menu"
                        class="inline-flex md:hidden items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black">
                    <svg x-show="!open" aria-hidden="true" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" aria-hidden="true" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M6 6l12 12M6 18L18 6" />
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobile-navigation" x-show="open" x-cloak class="md:hidden border-t border-gray-100 py-4">
            <form method="GET" action="{{ route('discover.index') }}" class="mb-4">
                <label for="mobile-navbar-search" class="sr-only">Search suppliers and materials</label>
                <div class="relative">
                    <x-heroicon-o-magnifying-glass aria-hidden="true" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                    <input id="mobile-navbar-search" name="query" type="search" value="{{ request('query') }}" placeholder="Search suppliers, materials..."
                           class="w-full rounded-lg bg-gray-100 border-none pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-black">
                </div>
            </form>

            <div class="space-y-1">
                <a href="{{ route('discover.index') }}" @click="open = false"
                   class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('discover.*') ? 'bg-gray-100 text-text-primary' : 'text-text-secondary hover:bg-gray-50' }}">Discover</a>
                <a href="{{ route('compare.index') }}" @click="open = false"
                   class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('compare.*') ? 'bg-gray-100 text-text-primary' : 'text-text-secondary hover:bg-gray-50' }}">Analytics</a>
                @unless (auth()->user()?->isPremium())
                    <a href="{{ route('upgrade') }}" @click="open = false" class="block rounded-lg px-3 py-2 text-sm font-medium text-text-secondary hover:bg-gray-50">Upgrade</a>
                @endunless
                @auth
                    <a href="{{ route('profile') }}" @click="open = false" class="block rounded-lg px-3 py-2 text-sm font-medium text-text-secondary hover:bg-gray-50">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-text-secondary hover:bg-gray-50">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" @click="open = false" class="block rounded-lg px-3 py-2 text-sm font-medium text-text-secondary hover:bg-gray-50">Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
