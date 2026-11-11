<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-xl text-gray-900 leading-tight">
                    {{ __('Manajemen Pemasok (CMS Portal)') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Kelola data direktori pemasok, saluran kontak langsung, serta berkas katalog & sertifikasi.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.suppliers.create') }}" class="px-4 py-2.5 rounded-xl bg-black hover:bg-gray-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5">
                    <x-heroicon-m-plus class="w-4 h-4" />
                    <span>Tambah Pemasok Baru</span>
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
                <span class="text-xs text-emerald-600 font-semibold uppercase tracking-wider">Tersinkronisasi</span>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-xs rounded-2xl border border-gray-200">
            <div class="p-6">
                <!-- Search bar & Filter -->
                <form method="GET" action="{{ route('admin.suppliers.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="relative md:col-span-5">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, WhatsApp, email, kategori, atau kota..."
                               class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                    <select name="category" onchange="this.form.submit()" class="md:col-span-3 text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">
                        <option value="">Semua kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                    <select name="status" onchange="this.form.submit()" class="md:col-span-2 text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">
                        <option value="">Semua status</option>
                        <option value="verified" @selected(request('status') === 'verified')>Terverifikasi</option>
                        <option value="standard" @selected(request('status') === 'standard')>Standar</option>
                    </select>
                    <select name="sort" onchange="this.form.submit()" class="md:col-span-2 text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">
                        <option value="newest" @selected(request('sort', 'newest') === 'newest')>Terbaru</option>
                        <option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option>
                        <option value="name" @selected(request('sort') === 'name')>Nama A-Z</option>
                    </select>
                    <div class="md:col-span-12 flex items-center gap-3">
                        <button type="submit" class="px-5 py-2 bg-black text-white font-semibold rounded-xl hover:bg-gray-800 transition text-xs shadow-xs">Cari</button>
                        @if(request()->hasAny(['q', 'category', 'status', 'sort']))
                            <a href="{{ route('admin.suppliers.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition text-xs">Reset</a>
                        @endif
                        <span class="text-2xs text-gray-400 ml-auto">{{ $suppliers->total() }} pemasok</span>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-surface">
                            <tr>
                                <th class="px-5 py-3.5 text-left font-bold text-xs text-gray-600 uppercase tracking-wider">Logo & Identitas</th>
                                <th class="px-5 py-3.5 text-left font-bold text-xs text-gray-600 uppercase tracking-wider">Kategori & Lokasi</th>
                                <th class="px-5 py-3.5 text-left font-bold text-xs text-gray-600 uppercase tracking-wider">Kontak Langsung</th>
                                <th class="px-5 py-3.5 text-center font-bold text-xs text-gray-600 uppercase tracking-wider">Dokumen</th>
                                <th class="px-5 py-3.5 text-left font-bold text-xs text-gray-600 uppercase tracking-wider">Kelengkapan</th>
                                <th class="px-5 py-3.5 text-left font-bold text-xs text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-right font-bold text-xs text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($suppliers as $supplier)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            @if($supplier->logo)
                                                <img src="{{ str_starts_with($supplier->logo, 'http') ? $supplier->logo : asset('storage/' . $supplier->logo) }}" alt="{{ $supplier->name }}" class="w-10 h-10 rounded-xl object-cover border border-gray-200" />
                                            @else
                                                <div class="w-10 h-10 rounded-xl bg-surface border border-gray-200 text-gray-800 font-bold flex items-center justify-center text-xs">
                                                    {{ strtoupper(substr($supplier->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $supplier->name }}</div>
                                                <div class="text-2xs text-gray-400 font-mono">{{ $supplier->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="text-gray-900 font-medium text-xs">{{ $supplier->industry_category }}</div>
                                        <div class="text-2xs text-gray-500 mt-0.5">{{ $supplier->city ? $supplier->city . ', ' : '' }}{{ $supplier->country }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="space-y-1">
                                            @if($supplier->whatsapp)
                                                <a href="{{ $supplier->whatsapp_url }}" target="_blank" class="inline-flex items-center gap-1 text-2xs font-semibold text-emerald-700 hover:underline">
                                                    <x-heroicon-o-chat-bubble-left-ellipsis class="w-3.5 h-3.5 text-emerald-600" /> {{ $supplier->whatsapp }}
                                                </a>
                                            @else
                                                <span class="text-2xs text-gray-400 block">— WA kosong</span>
                                            @endif

                                            @if($supplier->email)
                                                <div class="text-2xs text-gray-600 flex items-center gap-1 truncate max-w-[180px]">
                                                    <x-heroicon-o-envelope class="w-3.5 h-3.5 text-gray-400 shrink-0" /> {{ $supplier->email }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-lg {{ $supplier->documents_count > 0 ? 'bg-gray-100 text-gray-800' : 'bg-gray-50 text-gray-400' }}">
                                            <x-heroicon-o-document-text class="w-3.5 h-3.5" />
                                            {{ $supplier->documents_count }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @php
                                            $completeness = $supplier->profileCompleteness();
                                        @endphp
                                        <div class="flex items-center gap-2" title="Belum diisi: {{ implode(', ', $completeness['missing']) ?: '-' }}">
                                            <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full {{ $completeness['percent'] >= 80 ? 'bg-emerald-500' : 'bg-gray-800' }}" style="width: {{ $completeness['percent'] }}%"></div>
                                            </div>
                                            <span class="text-2xs font-semibold text-gray-600">{{ $completeness['percent'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <form method="POST" action="{{ route('admin.suppliers.verify', $supplier) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="Klik untuk mengubah status verifikasi"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-2xs font-bold border transition {{ $supplier->is_verified ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200' }}">
                                                @if($supplier->is_verified)
                                                    <x-heroicon-s-check-circle class="w-3 h-3" /> Terverifikasi
                                                @else
                                                    Standar
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('suppliers.show', $supplier->slug) }}" target="_blank" class="p-1.5 text-gray-500 hover:text-black hover:bg-gray-100 rounded-lg transition" title="Lihat di Web">
                                                <x-heroicon-m-arrow-top-right-on-square class="w-4 h-4" />
                                            </a>
                                            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="px-3 py-1.5 bg-black hover:bg-gray-800 text-white font-semibold rounded-lg transition" title="Edit Data & Dokumen">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}" onsubmit="return confirm('Peringatan: Yakin ingin menghapus seluruh data pemasok \'{{ addslashes($supplier->name) }}\'? Seluruh dokumen terkait juga akan terhapus.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus Pemasok">
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-xs">
                                        Tidak ditemukan pemasok yang sesuai dengan filter.
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
