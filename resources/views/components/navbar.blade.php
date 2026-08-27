<nav class="sticky top-0 z-40 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 gap-6">
            <div class="flex items-center gap-8">
                <a href="{{ route('discover.index') }}" class="font-bold text-xl text-text-primary shrink-0">
                    SupplyChain Pro
                </a>

                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('discover.index') }}"
                       class="text-sm font-medium pb-1 {{ request()->routeIs('discover.*') ? 'border-b-2 border-black font-semibold text-text-primary' : 'text-text-secondary' }}">
                        Discover
                    </a>
                    <a href="{{ route('compare.index') }}"
                       class="text-sm font-medium pb-1 {{ request()->routeIs('compare.*') ? 'border-b-2 border-black font-semibold text-text-primary' : 'text-text-secondary' }}">
                        Analytics
                    </a>
                    <span class="text-sm font-medium text-text-secondary">Messages</span>
                </div>
            </div>

            <div class="hidden sm:flex flex-1 max-w-md">
                <div class="relative w-full">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                    <input type="text" placeholder="Search suppliers, materials..."
                           class="w-full rounded-lg bg-gray-100 border-none pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-black">
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                @unless (auth()->user()?->isPremium())
                    <a href="{{ route('upgrade') }}" class="bg-black text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-gray-800">
                        Upgrade
                    </a>
                @endunless

                <button class="text-gray-500 hover:text-gray-700">
                    <x-heroicon-o-bell class="w-5 h-5" />
                </button>
                <button class="text-gray-500 hover:text-gray-700">
                    <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                </button>

                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false" class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                            <x-heroicon-o-user class="w-5 h-5 text-gray-500" />
                        </button>
                        <div x-show="open" x-cloak
                             class="absolute right-0 mt-2 w-44 bg-white rounded-lg border border-gray-200 shadow-lg py-1 text-sm">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 hover:bg-gray-50">Profile</a>
                            <a href="{{ route('upgrade') }}" class="block px-4 py-2 hover:bg-gray-50">Billing</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-50">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-text-secondary">Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
