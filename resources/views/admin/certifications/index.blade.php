<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-xl text-gray-900 leading-tight">
                    {{ __('Master Data Sertifikasi Standar') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Kelola sertifikasi mutu industri internasional, akreditasi lingkungan, dan standar keamanan pangan.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.certifications.create') }}" class="px-4 py-2.5 rounded-xl bg-black hover:bg-gray-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5">
                    <x-heroicon-m-plus class="w-4 h-4" />
                    <span>Tambah Sertifikasi Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @include('admin.partials.nav')

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl flex items-center justify-between text-sm shadow-xs">
                <div class="flex items-center gap-2.5">
                    <x-heroicon-s-check-circle class="w-5 h-5 text-emerald-600 shrink-0" />
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-xs rounded-2xl border border-gray-200">
            <div class="p-6">
                <!-- Search bar -->
                <form method="GET" action="{{ route('admin.certifications.index') }}" class="mb-6 flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari nama sertifikasi standar (misal: ISO, Halal, HACCP)..."
                            class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black"
                        />
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-black text-white font-semibold rounded-xl hover:bg-gray-800 transition text-xs shadow-xs">
                        Cari
                    </button>
                    @if(request('q'))
                        <a href="{{ route('admin.certifications.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition text-xs flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-surface">
                            <tr>
                                <th class="px-5 py-3.5 text-left font-bold text-xs text-gray-600 uppercase tracking-wider">Nama Sertifikasi</th>
                                <th class="px-5 py-3.5 text-left font-bold text-xs text-gray-600 uppercase tracking-wider">Kategori / Klasifikasi Mutu</th>
                                <th class="px-5 py-3.5 text-center font-bold text-xs text-gray-600 uppercase tracking-wider">Vendor Tersertifikasi</th>
                                <th class="px-5 py-3.5 text-right font-bold text-xs text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($certifications as $cert)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                                                <x-heroicon-o-shield-check class="w-4 h-4" />
                                            </div>
                                            <div class="font-bold text-gray-900">{{ $cert->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            {{ $cert->category }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-800">
                                            {{ $cert->suppliers_count }} vendor
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.certifications.edit', $cert) }}" class="px-3 py-1.5 bg-black hover:bg-gray-800 text-white font-semibold rounded-lg transition" title="Edit Sertifikasi">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.certifications.destroy', $cert) }}" onsubmit="return confirm('Peringatan: Yakin ingin menghapus sertifikasi \'{{ addslashes($cert->name) }}\'? Seluruh keterkaitan pada vendor terkait juga akan dilepas.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus Sertifikasi">
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-xs">
                                        Belum ada standar sertifikasi yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $certifications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
