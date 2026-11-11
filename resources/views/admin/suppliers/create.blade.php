<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.suppliers.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition flex items-center gap-1">
                    <x-heroicon-m-arrow-left class="w-4 h-4" /> Kembali
                </a>
                <h2 class="font-extrabold text-xl text-gray-900 leading-tight">Tambah Pemasok Baru</h2>
            </div>
            <span class="text-xs bg-black text-white px-3 py-1 rounded-full font-medium">CMS Namabiz</span>
        </div>
    </x-slot>

    <div class="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-sm">
                <p class="font-bold mb-1">Terdapat kesalahan pada formulir:</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.suppliers.store') }}" enctype="multipart/form-data"
              x-data="{ tab: 'all', saving: false }" @submit="saving = true" class="space-y-8">
            @csrf

            @include('admin.suppliers.partials.profile-fields')

            <x-admin.card title="Sertifikasi Standar" description="Centang sertifikasi yang dimiliki pemasok (opsional, bisa diubah nanti).">
                @include('admin.suppliers.partials.certification-fields')
            </x-admin.card>

            <x-admin.card title="Dokumen & Brosur" description="Unggah beberapa berkas sekaligus (opsional, bisa ditambah nanti).">
                @include('admin.suppliers.partials.document-uploader')
            </x-admin.card>

            <div class="sticky bottom-0 -mx-4 sm:mx-0 px-4 sm:px-0 py-4 bg-surface/90 backdrop-blur flex items-center justify-end gap-3 border-t border-gray-200 sm:border-0 sm:rounded-2xl">
                <a href="{{ route('admin.suppliers.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition">Batal</a>
                <button type="submit" :disabled="saving" class="px-6 py-2.5 rounded-xl bg-black hover:bg-gray-800 disabled:opacity-60 text-white font-semibold text-xs shadow-xs transition">
                    <span x-show="!saving">Simpan Pemasok</span>
                    <span x-show="saving" x-cloak>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
