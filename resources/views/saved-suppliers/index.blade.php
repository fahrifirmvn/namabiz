<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-xl text-gray-900 leading-tight">
                    {{ __('Pemasok Tersimpan & Proyek Pengadaan') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Kelola rekanan industri tersimpan, kategorikan ke proyek tender, dan bandingkan kapabilitas pasokan.
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if(count($currentSupplierIds) > 0)
                    <a href="{{ route('compare.index', ['suppliers' => array_slice($currentSupplierIds, 0, 4)]) }}"
                       class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-800 font-semibold text-xs shadow-2xs transition inline-flex items-center gap-1.5">
                        <x-heroicon-o-scale class="w-4 h-4 text-gray-500" />
                        <span>Bandingkan Terpilih ({{ min(count($currentSupplierIds), 4) }})</span>
                    </a>
                @endif
                <button type="button" @click="$dispatch('open-create-collection-modal')"
                        class="px-4 py-2.5 rounded-xl bg-black hover:bg-gray-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5">
                    <x-heroicon-m-plus class="w-4 h-4" />
                    <span>Buat Proyek Baru</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6"
         x-data="{
             createModal: false,
             editModal: false,
             activeEditCollection: { id: null, name: '', description: '' },
             openEdit(id, name, desc) {
                 this.activeEditCollection = { id: id, name: name, description: desc };
                 this.editModal = true;
             }
         }"
         @open-create-collection-modal.window="createModal = true">

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl flex items-center justify-between text-sm shadow-xs">
                <div class="flex items-center gap-2.5">
                    <x-heroicon-s-check-circle class="w-5 h-5 text-emerald-600 shrink-0" />
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-2xl text-sm">
                <p class="font-bold mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
            <!-- Sidebar: Proyek & Kategori Koleksi -->
            <aside class="space-y-6">
                <!-- Status & Filter Proyek Card -->
                <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Navigasi Pengadaan</h3>
                    <div class="space-y-1 text-sm font-medium">
                        <a href="{{ route('saved.index') }}"
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ empty($activeCollectionId) ? 'bg-black text-white font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                            <span class="flex items-center gap-2">
                                <x-heroicon-o-bookmark class="w-4 h-4 {{ empty($activeCollectionId) ? 'text-white' : 'text-gray-400' }}" />
                                <span>Semua Tersimpan</span>
                            </span>
                            <span class="text-2xs font-bold px-2 py-0.5 rounded-full {{ empty($activeCollectionId) ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700' }}">
                                {{ $totalSavedCount }}
                            </span>
                        </a>

                        <a href="{{ route('saved.index', ['collection' => 'unassigned']) }}"
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ $activeCollectionId === 'unassigned' ? 'bg-black text-white font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                            <span class="flex items-center gap-2">
                                <x-heroicon-o-folder class="w-4 h-4 {{ $activeCollectionId === 'unassigned' ? 'text-white' : 'text-gray-400' }}" />
                                <span>Tanpa Proyek</span>
                            </span>
                            <span class="text-2xs font-bold px-2 py-0.5 rounded-full {{ $activeCollectionId === 'unassigned' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700' }}">
                                {{ $unassignedCount }}
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Daftar Proyek Pengadaan Aktif -->
                <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Proyek Pengadaan</h3>
                        <button type="button" @click="createModal = true" class="text-xs font-semibold text-black hover:underline">
                            + Tambah
                        </button>
                    </div>

                    <div class="space-y-1.5">
                        @forelse($collections as $col)
                            <div class="group relative flex items-center justify-between p-2.5 rounded-xl border {{ $activeCollectionId == $col->id ? 'border-black bg-gray-50 font-bold' : 'border-transparent hover:bg-gray-50' }} transition">
                                <a href="{{ route('saved.index', ['collection' => $col->id]) }}" class="flex-1 min-w-0 pr-2">
                                    <p class="text-xs font-semibold text-gray-900 truncate">{{ $col->name }}</p>
                                    <p class="text-2xs text-gray-400">{{ $col->saved_suppliers_count }} pemasok terdaftar</p>
                                </a>

                                <div class="flex items-center gap-1 shrink-0 opacity-80 group-hover:opacity-100">
                                    <button type="button" @click="openEdit({{ $col->id }}, '{{ addslashes($col->name) }}', '{{ addslashes($col->description ?? '') }}')" class="p-1 text-gray-400 hover:text-black rounded" title="Edit Proyek">
                                        <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                                    </button>
                                    <form method="POST" action="{{ route('collections.destroy', $col) }}" onsubmit="return confirm('Hapus proyek \'{{ addslashes($col->name) }}\'? Vendor di dalamnya akan tetap tersimpan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-gray-400 hover:text-red-600 rounded" title="Hapus Proyek">
                                            <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl">
                                Belum ada proyek dibuat.<br>
                                <button type="button" @click="createModal = true" class="mt-2 text-black font-bold underline">
                                    Buat Proyek Pertama
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </aside>

            <!-- Main Content: Daftar Kartu Pemasok -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Search & Filter Bar -->
                <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                    <form method="GET" action="{{ route('saved.index') }}" class="w-full sm:w-80 relative">
                        @if($activeCollectionId)
                            <input type="hidden" name="collection" value="{{ $activeCollectionId }}">
                        @endif
                        <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari pemasok tersimpan..."
                            class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border-gray-300 focus:border-black focus:ring-black"
                        />
                    </form>

                    <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                        <span class="text-xs text-gray-500">
                            Menampilkan <strong>{{ $savedSuppliers->total() }}</strong> rekanan
                        </span>
                        @if($activeCollection && $activeCollection->saved_suppliers_count > 1)
                            @php
                                $colSupplierIds = $activeCollection->savedSuppliers->pluck('supplier_id')->filter()->take(4)->toArray();
                            @endphp
                            <a href="{{ route('compare.index', ['suppliers' => $colSupplierIds]) }}"
                               class="px-3.5 py-1.5 bg-black hover:bg-gray-800 text-white font-semibold text-xs rounded-xl transition inline-flex items-center gap-1 shadow-xs">
                                <x-heroicon-o-scale class="w-3.5 h-3.5" />
                                <span>Bandingkan Proyek</span>
                            </a>
                        @endif
                    </div>
                </div>

                @if($activeCollection)
                    <div class="bg-surface rounded-2xl border border-gray-200 p-5 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-extrabold text-base text-gray-900">{{ $activeCollection->name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $activeCollection->description ?: 'Tidak ada deskripsi proyek.' }}</p>
                            </div>
                            <span class="text-xs font-bold px-3 py-1 bg-white border border-gray-200 rounded-full text-gray-800 shadow-2xs">
                                {{ $activeCollection->saved_suppliers_count }} Pemasok
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Grid Kartu Pemasok -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @forelse($savedSuppliers as $saved)
                        @php
                            $s = $saved->supplier;
                        @endphp
                        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-2xs hover:shadow-md transition flex flex-col justify-between space-y-4">
                            <!-- Card Header -->
                            <div>
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <div class="w-11 h-11 rounded-xl border border-gray-200 bg-surface flex items-center justify-center font-extrabold text-sm text-gray-900 shrink-0">
                                            @if($s->logo)
                                                <img src="{{ str_starts_with($s->logo, 'http') ? $s->logo : asset('storage/' . $s->logo) }}" alt="{{ $s->name }}" class="w-full h-full object-cover rounded-xl" />
                                            @else
                                                {{ strtoupper(substr($s->name, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-sm text-gray-900 truncate">
                                                <a href="{{ route('suppliers.show', $s->slug) }}" class="hover:underline">{{ $s->name }}</a>
                                            </h4>
                                            <p class="text-2xs text-gray-500 mt-0.5 truncate">{{ $s->city ? $s->city . ', ' : '' }}{{ $s->country }} &bull; {{ $s->industry_category }}</p>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('saved.destroy', $saved) }}" onsubmit="return confirm('Hapus pemasok ini dari arsip simpan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-gray-400 hover:text-red-600 rounded-lg transition" title="Hapus dari tersimpan">
                                            <x-heroicon-o-bookmark-slash class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>

                                <!-- Tags & Specs -->
                                <div class="flex flex-wrap gap-1.5 mt-3">
                                    @foreach($s->tags->take(3) as $t)
                                        <span class="text-2xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded font-medium">{{ $t->tag_name }}</span>
                                    @endforeach
                                </div>

                                <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-gray-100 text-2xs text-gray-500">
                                    <div>
                                        <span class="block text-gray-400 uppercase font-semibold">MOQ (Min. Order)</span>
                                        <span class="font-bold text-gray-800">{{ $s->moq ?? 'Sesuai Diskusi' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-gray-400 uppercase font-semibold">Lead Time</span>
                                        <span class="font-bold text-gray-800">{{ $s->avg_lead_time ?? '2-4 Minggu' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer: Koleksi Proyek & Kontak -->
                            <div class="pt-3 border-t border-gray-100 space-y-3">
                                <!-- Pindahkan Koleksi Dropdown -->
                                <form method="POST" action="{{ route('saved.move', $saved) }}" class="flex items-center justify-between gap-2">
                                    @csrf
                                    <label class="text-2xs font-semibold text-gray-500 shrink-0">Proyek:</label>
                                    <select name="collection_id" onchange="this.form.submit()" class="text-2xs py-1 px-2.5 rounded-lg border-gray-300 bg-surface focus:border-black focus:ring-black flex-1">
                                        <option value="" {{ is_null($saved->collection_id) ? 'selected' : '' }}>— Tanpa Proyek —</option>
                                        @foreach($collections as $c)
                                            <option value="{{ $c->id }}" {{ $saved->collection_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </form>

                                <!-- Action Buttons: Kontak Langsung -->
                                <div class="flex items-center gap-2">
                                    @if($s->whatsapp_url)
                                        <a href="{{ $s->whatsapp_url }}" target="_blank" rel="noopener noreferrer"
                                           class="flex-1 text-center py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-2xs rounded-xl transition inline-flex items-center justify-center gap-1.5 shadow-2xs">
                                            <x-heroicon-o-chat-bubble-left-ellipsis class="w-3.5 h-3.5" />
                                            <span>WhatsApp</span>
                                        </a>
                                    @endif

                                    <a href="mailto:{{ $s->email ?: ($s->website ? 'sales@' . ltrim(parse_url($s->website, PHP_URL_HOST) ?? $s->website, 'www.') : 'contact@namabiz.com') }}"
                                       class="flex-1 text-center py-2 px-3 bg-black hover:bg-gray-800 text-white font-semibold text-2xs rounded-xl transition inline-flex items-center justify-center gap-1.5 shadow-2xs">
                                        <x-heroicon-o-envelope class="w-3.5 h-3.5" />
                                        <span>Email</span>
                                    </a>

                                    <a href="{{ route('suppliers.show', $s->slug) }}"
                                       class="p-2 border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 rounded-xl transition shadow-2xs" title="Lihat Profil Lengkap">
                                        <x-heroicon-m-arrow-top-right-on-square class="w-3.5 h-3.5" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-dashed border-gray-200 p-8 space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-surface border border-gray-200 text-gray-400 flex items-center justify-center mx-auto shadow-2xs">
                                <x-heroicon-o-bookmark class="w-6 h-6" />
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm">Tidak ada pemasok yang ditemukan</h4>
                            <p class="text-xs text-gray-500 max-w-sm mx-auto">
                                Anda belum menyimpan pemasok ke kategori proyek ini. Buka direktori untuk menemukan rekanan industri pilihan.
                            </p>
                            <div class="pt-2">
                                <a href="{{ route('discover.index') }}" class="px-5 py-2.5 bg-black hover:bg-gray-800 text-white font-semibold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-1.5">
                                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                                    <span>Jelajahi Direktori Pemasok</span>
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $savedSuppliers->links() }}
                </div>
            </div>
        </div>

        <!-- ================= MODAL BUAT PROYEK BARU ================= -->
        <div x-show="createModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="createModal" @click="createModal = false" class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="createModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <form method="POST" action="{{ route('collections.store') }}" class="p-6 sm:p-8 space-y-5">
                        @csrf
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <h3 class="text-base font-bold text-gray-900">Buat Proyek Pengadaan Baru</h3>
                            <button type="button" @click="createModal = false" class="text-gray-400 hover:text-black">
                                <x-heroicon-m-x-mark class="w-5 h-5" />
                            </button>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Proyek / Kategori Tender *</label>
                            <input type="text" name="name" required placeholder="Contoh: Proyek Kemasan Kopi Ramah Lingkungan 2026" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi / Catatan Internal</label>
                            <textarea name="description" rows="3" placeholder="Tujuan proyek, target kuantiti, atau syarat vendor khusus..." class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="createModal = false" class="px-4 py-2 text-xs font-semibold text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-black rounded-xl hover:bg-gray-800 transition shadow-xs">
                                Simpan Proyek
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================= MODAL EDIT PROYEK ================= -->
        <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModal" @click="editModal = false" class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="editModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <form method="POST" :action="'{{ url('procurement-collections') }}/' + activeEditCollection.id" class="p-6 sm:p-8 space-y-5">
                        @csrf
                        @method('PUT')
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <h3 class="text-base font-bold text-gray-900">Ubah Proyek Pengadaan</h3>
                            <button type="button" @click="editModal = false" class="text-gray-400 hover:text-black">
                                <x-heroicon-m-x-mark class="w-5 h-5" />
                            </button>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Proyek *</label>
                            <input type="text" name="name" x-model="activeEditCollection.name" required class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi / Catatan Internal</label>
                            <textarea name="description" rows="3" x-model="activeEditCollection.description" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="editModal = false" class="px-4 py-2 text-xs font-semibold text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-black rounded-xl hover:bg-gray-800 transition shadow-xs">
                                Perbarui Proyek
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
