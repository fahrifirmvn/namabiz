<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.certifications.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition flex items-center gap-1">
                    <x-heroicon-m-arrow-left class="w-4 h-4" /> Kembali
                </a>
                <h2 class="font-extrabold text-xl text-gray-900 leading-tight">
                    Edit Sertifikasi: <span class="text-gray-600">{{ $certification->name }}</span>
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

        <form method="POST" action="{{ route('admin.certifications.update', $certification) }}" class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-xs space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Sertifikasi / Standar Mutu *</label>
                <input type="text" name="name" value="{{ old('name', $certification->name) }}" required class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Klasifikasi / Kategori Mutu *</label>
                <input list="categories_list" type="text" name="category" value="{{ old('category', $certification->category) }}" required class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                <datalist id="categories_list">
                    @foreach($existingCategories as $exCat)
                        <option value="{{ $exCat }}"></option>
                    @endforeach
                    <option value="Quality Management"></option>
                    <option value="Food Safety"></option>
                    <option value="Environmental"></option>
                    <option value="Occupational Health & Safety"></option>
                    <option value="Social & Ethical Compliance"></option>
                </datalist>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.certifications.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-black hover:bg-gray-800 text-white font-semibold text-xs shadow-xs transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
