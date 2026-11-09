<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.suppliers.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition flex items-center gap-1">
                    <x-heroicon-m-arrow-left class="w-4 h-4" /> Kembali
                </a>
                <h2 class="font-extrabold text-xl text-gray-900 leading-tight">
                    Tambah Pemasok Baru
                </h2>
            </div>
            <span class="text-xs bg-black text-white px-3 py-1 rounded-full font-medium">
                CMS Namabiz
            </span>
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

        <form method="POST" action="{{ route('admin.suppliers.store') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- 1. Identitas Perusahaan & Logo -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-xs space-y-6">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Identitas & Logo Pemasok</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Informasi dasar nama entitas usaha dan representasi visual.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nama Perusahaan / Pabrik *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: PT Sumber Plastik Lestari" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kategori Industri *</label>
                        <select name="industry_category" required class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">
                            <option value="">Pilih Kategori</option>
                            @foreach(['Packaging Manufacturer', 'Distributor & Converter', 'Contract Manufacturer', 'Raw Material Supplier', 'Spices & Coconut', 'Logistics & Warehousing', 'Chemical & Ingredients'] as $cat)
                                <option value="{{ $cat }}" {{ old('industry_category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tagline Singkat</label>
                        <input type="text" name="tagline" value="{{ old('tagline') }}" placeholder="Contoh: Produsen kemasan fleksibel ramah lingkungan" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                </div>

                <!-- Logo Section -->
                <div class="p-5 bg-surface rounded-xl border border-gray-200 space-y-4">
                    <label class="block text-xs font-bold text-gray-900">Logo Perusahaan</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-2xs text-gray-500 mb-1.5">Unggah Berkas Gambar (PNG/JPG, maks 2MB)</span>
                            <input type="file" name="logo_file" accept="image/*" class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800 transition" />
                        </div>
                        <div>
                            <span class="block text-2xs text-gray-500 mb-1.5">Atau Tautan URL Gambar Langsung</span>
                            <input type="url" name="logo_url" value="{{ old('logo_url') }}" placeholder="https://example.com/logo.png" class="w-full text-xs rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Kontak Langsung (WhatsApp, Email & Web) -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-xs space-y-6">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Saluran Kontak Langsung</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Klien B2B akan menghubungi pemasok langsung melalui kontak ini.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <span class="flex items-center gap-1.5">
                                <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4 text-emerald-600" /> Nomor WhatsApp
                            </span>
                        </label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="Contoh: 081234567890" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                        <span class="text-2xs text-gray-400 mt-1 block">Otomatis diformat ke tautan wa.me/62...</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <span class="flex items-center gap-1.5">
                                <x-heroicon-o-envelope class="w-4 h-4 text-gray-600" /> Email Resmi
                            </span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="sales@perusahaan.co.id" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <span class="flex items-center gap-1.5">
                                <x-heroicon-o-globe-alt class="w-4 h-4 text-gray-600" /> Website
                            </span>
                        </label>
                        <input type="text" name="website" value="{{ old('website') }}" placeholder="https://perusahaan.co.id" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Negara *</label>
                        <input type="text" name="country" value="{{ old('country', 'Indonesia') }}" required class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kota / Lokasi Pabrik</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="Contoh: Surabaya, Jawa Timur" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                </div>
            </div>

            <!-- 3. Profil & Deskripsi -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-xs space-y-6">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Profil & Narasi Perusahaan</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Penjelasan lengkap spesialisasi dan latar belakang pemasok.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tentang (About Singkat)</label>
                        <textarea name="about" rows="3" placeholder="Profil umum sejarah perusahaan..." class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">{{ old('about') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Tambahan / Keunggulan</label>
                        <textarea name="description" rows="3" placeholder="Keunggulan fasilitas produksi, sertifikasi higienis..." class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 4. Metrik Operasional & Verifikasi -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-xs space-y-6">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Spesifikasi Operasional & Status</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Data metrik yang menjadi filter perbandingan pembeli.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">MOQ (Pesanan Min.)</label>
                        <input type="text" name="moq" value="{{ old('moq') }}" placeholder="Contoh: 10.000 unit" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kapasitas Produksi</label>
                        <input type="text" name="production_capacity" value="{{ old('production_capacity') }}" placeholder="Contoh: 500.000 unit / bulan" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Lead Time Standar</label>
                        <input type="text" name="avg_lead_time" value="{{ old('avg_lead_time') }}" placeholder="Contoh: 2-3 minggu" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Jumlah Karyawan / Ukuran</label>
                        <input type="text" name="company_size" value="{{ old('company_size') }}" placeholder="Contoh: 250 - 500 Karyawan" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tahun Berdiri</label>
                        <input type="number" name="founded_year" value="{{ old('founded_year') }}" placeholder="Contoh: 2012" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Jejak Karbon / ISO Status</label>
                        <input type="text" name="carbon_footprint_report" value="{{ old('carbon_footprint_report') }}" placeholder="Contoh: Terakreditasi ISO 14001" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_verified" value="1" {{ old('is_verified') ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black" />
                        <span class="text-sm font-bold text-gray-900">Tandai sebagai Pemasok Terverifikasi Namabiz (Verified Badge)</span>
                    </label>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.suppliers.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-black hover:bg-gray-800 text-white font-semibold text-xs shadow-xs transition">
                    Simpan & Lanjutkan ke Dokumen &rarr;
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
