<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <a href="{{ route('admin.suppliers.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition flex items-center gap-1 shrink-0">
                    <x-heroicon-m-arrow-left class="w-4 h-4" /> Kembali ke Daftar
                </a>
                <h2 class="font-extrabold text-xl text-gray-900 leading-tight truncate">
                    Edit Pemasok: <span class="text-gray-600">{{ $supplier->name }}</span>
                </h2>
            </div>
            <a href="{{ route('suppliers.show', $supplier->slug) }}" target="_blank" class="shrink-0 px-3.5 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 transition inline-flex items-center gap-1.5 shadow-2xs">
                <x-heroicon-m-arrow-top-right-on-square class="w-3.5 h-3.5 text-gray-400" />
                <span>Lihat Profil Publik</span>
            </a>
        </div>
    </x-slot>

    <div class="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6"
         x-data="{ tab: '{{ session('tab', request('tab', 'profil')) }}', saving: false }">
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl flex items-center gap-2.5 text-sm shadow-xs">
                <x-heroicon-s-check-circle class="w-5 h-5 text-emerald-600 shrink-0" />
                <span class="font-medium">{{ session('success') }}</span>
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

        <!-- Kelengkapan profil -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Kelengkapan Profil</h3>
                    @if($completeness['missing'])
                        <p class="text-2xs text-gray-500 mt-0.5">Belum diisi: {{ implode(', ', $completeness['missing']) }}</p>
                    @else
                        <p class="text-2xs text-emerald-700 mt-0.5">Profil sudah lengkap.</p>
                    @endif
                </div>
                <span class="text-lg font-extrabold {{ $completeness['percent'] >= 80 ? 'text-emerald-700' : 'text-gray-900' }}">{{ $completeness['percent'] }}%</span>
            </div>
            <div class="mt-3 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full {{ $completeness['percent'] >= 80 ? 'bg-emerald-500' : 'bg-black' }}" style="width: {{ $completeness['percent'] }}%"></div>
            </div>
        </div>

        <!-- Tabs -->
        @php
            $tabs = [
                'profil' => 'Profil',
                'kontak' => 'Kontak & Lokasi',
                'operasional' => 'Operasional & Tag',
                'sertifikasi' => 'Sertifikasi',
                'dokumen' => 'Dokumen (' . $supplier->documents->count() . ')',
                'ulasan' => 'Ulasan (' . $supplier->reviews_count . ')',
            ];
        @endphp
        <nav class="flex gap-6 overflow-x-auto border-b border-gray-200 text-sm font-semibold">
            @foreach($tabs as $key => $label)
                <button type="button" @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'border-black text-gray-900 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800'"
                        class="pb-3 -mb-px border-b-2 whitespace-nowrap transition">{{ $label }}</button>
            @endforeach
        </nav>

        <!-- Form utama: profil, kontak, operasional & tag -->
        <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" enctype="multipart/form-data"
              x-show="['profil', 'kontak', 'operasional'].includes(tab)" @submit="saving = true" class="space-y-6">
            @csrf
            @method('PUT')

            @include('admin.suppliers.partials.profile-fields')

            <div class="sticky bottom-0 py-4 bg-surface/90 backdrop-blur flex items-center justify-end gap-3">
                <a href="{{ route('admin.suppliers.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition">Batal</a>
                <button type="submit" :disabled="saving" class="px-6 py-2.5 rounded-xl bg-black hover:bg-gray-800 disabled:opacity-60 text-white font-semibold text-xs shadow-xs transition">
                    <span x-show="!saving">Simpan Perubahan</span>
                    <span x-show="saving" x-cloak>Menyimpan...</span>
                </button>
            </div>
        </form>

        <!-- Sertifikasi -->
        <div x-show="tab === 'sertifikasi'" x-cloak>
            <x-admin.card title="Sertifikasi Standar & Legalitas Mutu" description="Tandai sertifikasi yang dimiliki dan status validasinya.">
                <x-slot name="action">
                    <a href="{{ route('admin.certifications.index') }}" class="text-xs font-semibold text-black hover:underline">Kelola Master Sertifikasi &rarr;</a>
                </x-slot>
                <form method="POST" action="{{ route('admin.suppliers.certifications.sync', $supplier) }}" class="space-y-4">
                    @csrf
                    @include('admin.suppliers.partials.certification-fields')
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-black hover:bg-gray-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5">
                            <x-heroicon-o-shield-check class="w-4 h-4" /> <span>Simpan Sertifikasi</span>
                        </button>
                    </div>
                </form>
            </x-admin.card>
        </div>

        <!-- Dokumen -->
        <div x-show="tab === 'dokumen'" x-cloak class="space-y-6">
            <x-admin.card title="Dokumen Terpasang" description="Berkas publikasi, spesifikasi produk, dan bukti legalitas.">
                <x-slot name="action">
                    <span class="text-xs font-bold bg-gray-100 text-gray-800 px-3 py-1 rounded-full">{{ $supplier->documents->count() }} dokumen</span>
                </x-slot>

                <div class="space-y-3">
                    @forelse($supplier->documents as $doc)
                        <div class="flex items-center justify-between p-4 rounded-xl border border-gray-200 hover:bg-gray-50/50 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-surface border border-gray-200 flex items-center justify-center text-gray-700 shrink-0">
                                    <x-heroicon-o-document-text class="w-5 h-5" />
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-bold text-gray-900 truncate">{{ $doc->title }}</h4>
                                    <div class="flex items-center gap-2 mt-0.5 text-2xs text-gray-500">
                                        <span class="uppercase font-semibold bg-gray-100 px-1.5 py-0.5 rounded">{{ $doc->type ?? 'PDF' }}</span>
                                        @if($doc->is_premium)
                                            <span class="font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full inline-flex items-center gap-1"><x-heroicon-s-lock-closed class="w-3 h-3" /> Khusus Premium</span>
                                        @else
                                            <span class="font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">Akses Publik</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if($doc->file_path || $doc->external_url)
                                    <a href="{{ route('documents.download', $doc) }}" class="p-2 text-gray-600 hover:text-black hover:bg-gray-100 rounded-lg transition" title="Unduh Berkas">
                                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('admin.suppliers.documents.destroy', [$supplier, $doc]) }}" onsubmit="return confirm('Yakin ingin menghapus berkas \'{{ addslashes($doc->title) }}\'?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition" title="Hapus Dokumen">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-gray-400 bg-surface rounded-xl border border-dashed border-gray-200">Belum ada dokumen yang diunggah.</div>
                    @endforelse
                </div>
            </x-admin.card>

            <x-admin.card title="Unggah Dokumen Baru" description="Pilih beberapa berkas sekaligus; judul terisi otomatis dari nama berkas.">
                <form method="POST" action="{{ route('admin.suppliers.documents.bulk', $supplier) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @include('admin.suppliers.partials.document-uploader')
                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-black hover:bg-gray-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5">
                            <x-heroicon-o-cloud-arrow-up class="w-4 h-4" /> <span>Unggah Semua</span>
                        </button>
                    </div>
                </form>
            </x-admin.card>
        </div>

        <!-- Ulasan -->
        <div x-show="tab === 'ulasan'" x-cloak>
        <!-- Panel Moderasi Ulasan & Tanggapan Resmi Pemasok -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <x-heroicon-o-star class="w-5 h-5 text-amber-500" />
                        <span>Moderasi Ulasan & Tanggapan Resmi Vendor</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Kelola ulasan pembeli, tinjau parameter evaluasi mutu, dan publikasikan tanggapan resmi atas nama vendor.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-2.5 py-1 bg-amber-50 text-amber-900 border border-amber-200 rounded-full">
                        ★ {{ $supplier->average_rating ? number_format($supplier->average_rating, 1) : '0.0' }} ({{ $supplier->reviews_count }} ulasan)
                    </span>
                </div>
            </div>

            <div class="space-y-6">
                @forelse($supplier->reviews as $rev)
                    <div class="p-5 rounded-2xl bg-surface border border-gray-200 space-y-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-bold text-sm text-gray-900">{{ $rev->user?->name ?? 'Pengguna Pembeli' }}</h4>
                                    <span class="text-2xs text-gray-400">({{ $rev->user?->email }})</span>
                                    @if($rev->is_verified_buyer)
                                        <span class="text-2xs bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 rounded-full font-semibold">
                                            Pembeli Terverifikasi
                                        </span>
                                    @endif
                                </div>
                                <p class="text-2xs text-gray-500 mt-0.5">
                                    {{ $rev->reviewer_role ? $rev->reviewer_role . ' di ' : '' }}{{ $rev->reviewer_company ?: '-' }} &bull; {{ $rev->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>

                            <form method="POST" action="{{ route('admin.suppliers.reviews.destroy', [$supplier, $rev]) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan ini secara permanen?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition" title="Hapus Ulasan Moderasi">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </form>
                        </div>

                        <!-- Ratings Grid -->
                        <div class="flex flex-wrap items-center gap-2 text-2xs">
                            <span class="bg-amber-100 text-amber-900 font-bold px-2.5 py-1 rounded-md">
                                Keseluruhan: {{ $rev->rating }}/5 ★
                            </span>
                            <span class="bg-gray-100 text-gray-700 font-medium px-2.5 py-1 rounded-md">
                                Kualitas: {{ $rev->quality_rating }}/5
                            </span>
                            <span class="bg-gray-100 text-gray-700 font-medium px-2.5 py-1 rounded-md">
                                Lead Time: {{ $rev->delivery_rating }}/5
                            </span>
                            <span class="bg-gray-100 text-gray-700 font-medium px-2.5 py-1 rounded-md">
                                Layanan: {{ $rev->service_rating }}/5
                            </span>
                        </div>

                        <!-- Review Text -->
                        <div class="bg-white p-3.5 rounded-xl border border-gray-100 text-xs">
                            <h5 class="font-bold text-gray-900 mb-1">{{ $rev->title }}</h5>
                            <p class="text-gray-700 leading-relaxed">{{ $rev->comment }}</p>
                        </div>

                        <!-- Official Reply Form -->
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <h5 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-1.5">
                                <x-heroicon-s-chat-bubble-left-right class="w-4 h-4 text-black" />
                                <span>Tanggapan Resmi Vendor / Admin</span>
                                @if($rev->replied_at)
                                    <span class="text-2xs font-normal text-gray-500">(Dibalas {{ $rev->replied_at->format('d M Y, H:i') }})</span>
                                @endif
                            </h5>

                            <form method="POST" action="{{ route('admin.suppliers.reviews.reply', [$supplier, $rev]) }}" class="space-y-3">
                                @csrf
                                <textarea name="supplier_reply" rows="2" required placeholder="Tulis klarifikasi atau ucapan terima kasih resmi dari pihak vendor pemasok..." class="w-full text-xs rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black leading-relaxed">{{ old('supplier_reply', $rev->supplier_reply) }}</textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-1.5 bg-black hover:bg-gray-800 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                                        {{ $rev->supplier_reply ? 'Perbarui Tanggapan' : 'Publikasikan Tanggapan Resmi' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-gray-400 bg-surface rounded-xl border border-dashed border-gray-200">
                        Belum ada ulasan pembeli untuk pemasok ini.
                    </div>
                @endforelse
            </div>
        </div>
        </div>
    </div>
</x-app-layout>
