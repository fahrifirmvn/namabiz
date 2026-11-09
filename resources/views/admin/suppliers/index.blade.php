<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Supplier Management (Admin Portal)') }}
            </h2>
            <span class="text-xs bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full font-medium">
                Live Sync Enabled with Mobile App
            </span>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg flex items-center justify-between shadow-sm">
                <span>{{ session('success') }}</span>
                <span class="text-xs text-emerald-600 font-semibold">Changes synced to API</span>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
            <div class="p-6">
                <!-- Search bar -->
                <form method="GET" action="{{ route('admin.suppliers.index') }}" class="mb-6 flex gap-4">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search supplier name, country, or category..."
                        class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                    />
                    <button type="submit" class="px-5 py-2.5 bg-emerald-700 text-white font-medium rounded-lg hover:bg-emerald-800 transition text-sm">
                        Search
                    </button>
                    @if(request('q'))
                        <a href="{{ route('admin.suppliers.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition text-sm">
                            Clear
                        </a>
                    @endif
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Logo / Name</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($suppliers as $supplier)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            @if($supplier->logo)
                                                <img src="{{ str_starts_with($supplier->logo, 'http') ? $supplier->logo : asset('storage/' . $supplier->logo) }}" alt="{{ $supplier->name }}" class="w-10 h-10 rounded-lg object-cover border" />
                                            @else
                                                <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                                                    {{ strtoupper(substr($supplier->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $supplier->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $supplier->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600 font-medium">
                                        {{ $supplier->industry_category }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {{ $supplier->city ? $supplier->city . ', ' : '' }}{{ $supplier->country }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($supplier->is_verified)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                Verified
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                                Standard
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-xs rounded-md transition">
                                            Edit & Change Logo →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                        No suppliers found matching your query.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
