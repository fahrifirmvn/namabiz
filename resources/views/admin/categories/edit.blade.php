<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.categories.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition flex items-center gap-1">
                    <x-heroicon-m-arrow-left class="w-4 h-4" /> Kembali
                </a>
                <h2 class="font-extrabold text-xl text-gray-900 leading-tight">
                    Edit Kategori: <span class="text-gray-600">{{ $category->name }}</span>
                </h2>
            </div>
            <span class="text-xs bg-black text-white px-3 py-1 rounded-full font-medium">
                Master Data
            </span>
        </div>
    </x-slot>

    <div class="py-10 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-sm">
                <p class="font-bold mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-xs space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Kategori Industri *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                <span class="text-2xs text-gray-400 mt-1 block">Perubahan nama akan otomatis disinkronkan ke seluruh vendor yang telah menggunakannya.</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Slug URL</label>
                <input type="text" disabled value="{{ $category->slug }}" class="w-full text-sm rounded-xl border-gray-200 bg-gray-50 text-gray-500 font-mono shadow-xs" />
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Ruang Lingkup</label>
                <textarea name="description" rows="3" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-black hover:bg-gray-800 text-white font-semibold text-xs shadow-xs transition">
                    Simpan Perubahan Kategori
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
