<div class="border-b border-gray-200 mb-8">
    <nav class="-mb-px flex gap-6 overflow-x-auto text-sm font-semibold">
        <a href="{{ route('admin.suppliers.index') }}"
           class="pb-3 px-1 border-b-2 flex items-center gap-2 whitespace-nowrap transition {{ request()->routeIs('admin.suppliers.*') ? 'border-black text-gray-900 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <x-heroicon-o-building-office class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.suppliers.*') ? 'text-black' : 'text-gray-400' }}" />
            <span>Direktori Pemasok</span>
        </a>

        <a href="{{ route('admin.categories.index') }}"
           class="pb-3 px-1 border-b-2 flex items-center gap-2 whitespace-nowrap transition {{ request()->routeIs('admin.categories.*') ? 'border-black text-gray-900 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <x-heroicon-o-squares-2x2 class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.categories.*') ? 'text-black' : 'text-gray-400' }}" />
            <span>Kategori Industri</span>
        </a>

        <a href="{{ route('admin.certifications.index') }}"
           class="pb-3 px-1 border-b-2 flex items-center gap-2 whitespace-nowrap transition {{ request()->routeIs('admin.certifications.*') ? 'border-black text-gray-900 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <x-heroicon-o-shield-check class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.certifications.*') ? 'text-black' : 'text-gray-400' }}" />
            <span>Sertifikasi Standar</span>
        </a>
    </nav>
</div>
