<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.suppliers.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition flex items-center gap-1">
                    <x-heroicon-m-arrow-left class="w-4 h-4" /> Kembali ke Daftar
                </a>
                <h2 class="font-extrabold text-xl text-gray-900 leading-tight">
                    Edit Pemasok: <span class="text-gray-600">{{ $supplier->name }}</span>
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('suppliers.show', $supplier->slug) }}" target="_blank" class="px-3.5 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 transition inline-flex items-center gap-1.5 shadow-2xs">
                    <x-heroicon-m-arrow-top-right-on-square class="w-3.5 h-3.5 text-gray-400" />
                    <span>Lihat Profil Publik</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
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

        <!-- Form Update Data Pemasok -->
        <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- 1. Identitas Perusahaan & Logo -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-xs space-y-6">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Identitas & Logo Pemasok</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Slug: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700 font-mono">{{ $supplier->slug }}</code></p>
                </div>

                <!-- Preview & Upload Logo -->
                <div class="p-5 bg-surface rounded-xl border border-gray-200 flex flex-col sm:flex-row items-center gap-6">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden border border-gray-200 bg-white flex items-center justify-center shrink-0 shadow-2xs">
                        @if($supplier->logo)
                            <img src="{{ str_starts_with($supplier->logo, 'http') ? $supplier->logo : asset('storage/' . $supplier->logo) }}" alt="{{ $supplier->name }}" class="w-full h-full object-cover" />
                        @else
                            <span class="text-black font-extrabold text-xl">{{ strtoupper(substr($supplier->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 space-y-3 w-full">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Ganti Berkas Logo (PNG/JPG, maks 2MB)</label>
                            <input type="file" name="logo_file" accept="image/*" class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800 transition" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Atau Tautan URL Gambar</label>
                            <input type="url" name="logo_url" value="{{ old('logo_url', str_starts_with($supplier->logo ?? '', 'http') ? $supplier->logo : '') }}" placeholder="https://example.com/logo.png" class="w-full text-xs rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nama Perusahaan / Pabrik *</label>
                        <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kategori Industri *</label>
                        <select name="industry_category" required class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}" {{ (old('industry_category', $supplier->industry_category) == $cat->name) ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tagline Singkat</label>
                        <input type="text" name="tagline" value="{{ old('tagline', $supplier->tagline) }}" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                </div>
            </div>

            <!-- 2. Kontak Langsung (WhatsApp, Email & Web) -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-xs space-y-6">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Saluran Kontak Langsung</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Pelanggan langsung terhubung via WhatsApp & Email tanpa perantara.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <span class="flex items-center gap-1.5">
                                <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4 text-emerald-600" /> Nomor WhatsApp
                            </span>
                        </label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $supplier->whatsapp) }}" placeholder="Contoh: 081234567890" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                        @if($supplier->whatsapp_url)
                            <a href="{{ $supplier->whatsapp_url }}" target="_blank" class="text-2xs text-emerald-700 hover:underline mt-1 block">
                                Uji buka chat: {{ $supplier->whatsapp }} &rarr;
                            </a>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <span class="flex items-center gap-1.5">
                                <x-heroicon-o-envelope class="w-4 h-4 text-gray-600" /> Email Resmi
                            </span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $supplier->email) }}" placeholder="sales@perusahaan.co.id" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <span class="flex items-center gap-1.5">
                                <x-heroicon-o-globe-alt class="w-4 h-4 text-gray-600" /> Website
                            </span>
                        </label>
                        <input type="text" name="website" value="{{ old('website', $supplier->website) }}" placeholder="https://perusahaan.co.id" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Negara *</label>
                        <input type="text" name="country" value="{{ old('country', $supplier->country) }}" required class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kota / Lokasi Pabrik</label>
                        <input type="text" name="city" value="{{ old('city', $supplier->city) }}" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
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
                        <textarea name="about" rows="3" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">{{ old('about', $supplier->about) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Tambahan / Keunggulan</label>
                        <textarea name="description" rows="3" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">{{ old('description', $supplier->description) }}</textarea>
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
                        <input type="text" name="moq" value="{{ old('moq', $supplier->moq) }}" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kapasitas Produksi</label>
                        <input type="text" name="production_capacity" value="{{ old('production_capacity', $supplier->production_capacity) }}" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Lead Time Standar</label>
                        <input type="text" name="avg_lead_time" value="{{ old('avg_lead_time', $supplier->avg_lead_time) }}" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Jumlah Karyawan / Ukuran</label>
                        <input type="text" name="company_size" value="{{ old('company_size', $supplier->company_size) }}" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tahun Berdiri</label>
                        <input type="number" name="founded_year" value="{{ old('founded_year', $supplier->founded_year) }}" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Jejak Karbon / ISO Status</label>
                        <input type="text" name="carbon_footprint_report" value="{{ old('carbon_footprint_report', $supplier->carbon_footprint_report) }}" class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_verified" value="1" {{ old('is_verified', $supplier->is_verified) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black" />
                        <span class="text-sm font-bold text-gray-900">Tandai sebagai Pemasok Terverifikasi Namabiz (Verified Badge)</span>
                    </label>
                </div>
            </div>

            <!-- Tombol Aksi Simpan -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.suppliers.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-black hover:bg-gray-800 text-white font-semibold text-xs shadow-xs transition">
                    Simpan Perubahan Data
                </button>
            </div>
        </form>

        <!-- ================= PANEL MANAJEMEN SERTIFIKASI PEMASOK ================= -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Sertifikasi Standar & Legalitas Mutu</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Tandai sertifikasi resmi yang dimiliki pemasok dan tentukan status validasi auditnya.</p>
                </div>
                <a href="{{ route('admin.certifications.index') }}" class="text-xs font-semibold text-black hover:underline flex items-center gap-1">
                    Kelola Master Sertifikasi &rarr;
                </a>
            </div>

            <form method="POST" action="{{ route('admin.suppliers.certifications.sync', $supplier) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $assignedCertMap = $supplier->certifications->keyBy('certification_id');
                    @endphp
                    @foreach($certifications as $cert)
                        @php
                            $assigned = $assignedCertMap->get($cert->id);
                        @endphp
                        <div class="p-4 rounded-xl border {{ $assigned ? 'border-emerald-300 bg-emerald-50/20' : 'border-gray-200 bg-surface' }} space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="certifications[{{ $cert->id }}][active]" value="1" {{ $assigned ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black" />
                                    <span class="text-xs font-bold text-gray-900">{{ $cert->name }}</span>
                                </label>
                                <span class="text-2xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-medium">
                                    {{ $cert->category }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 text-xs">
                                <div>
                                    <label class="block text-2xs font-semibold text-gray-600 mb-1">Status Validasi</label>
                                    <select name="certifications[{{ $cert->id }}][status]" class="w-full text-2xs rounded-lg border-gray-300 py-1 px-2 focus:border-black focus:ring-black">
                                        <option value="verified" {{ ($assigned?->status === 'verified' || !$assigned) ? 'selected' : '' }}>Terverifikasi Resmi</option>
                                        <option value="partial" {{ $assigned?->status === 'partial' ? 'selected' : '' }}>Dalam Proses Audit</option>
                                        <option value="not_verified" {{ $assigned?->status === 'not_verified' ? 'selected' : '' }}>Belum Terverifikasi</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-2xs font-semibold text-gray-600 mb-1">Akses Informasi</label>
                                    <label class="inline-flex items-center gap-1.5 mt-1 cursor-pointer">
                                        <input type="checkbox" name="certifications[{{ $cert->id }}][is_premium_field]" value="1" {{ $assigned?->is_premium_field ? 'checked' : '' }} class="w-3.5 h-3.5 rounded border-gray-300 text-black focus:ring-black" />
                                        <span class="text-2xs text-gray-700">Khusus Premium</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end pt-3">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-black hover:bg-gray-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5">
                        <x-heroicon-o-shield-check class="w-4 h-4" />
                        <span>Simpan Pengaturan Sertifikasi</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ================= PANEL MANAJEMEN DOKUMEN (PDF) ================= -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Dokumen Profil & Sertifikasi (PDF)</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola berkas publikasi, spesifikasi produk, dan bukti legalitas pemasok.</p>
                </div>
                <span class="text-xs font-bold bg-gray-100 text-gray-800 px-3 py-1 rounded-full">
                    {{ $supplier->documents->count() }} Dokumen Terpasang
                </span>
            </div>

            <!-- List Dokumen yang Sudah Ada -->
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
                                        <span class="font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                                            <x-heroicon-s-lock-closed class="w-3 h-3" /> Khusus Akun Premium
                                        </span>
                                    @else
                                        <span class="font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                            Akses Publik Bebas
                                        </span>
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
                    <div class="py-8 text-center text-xs text-gray-400 bg-surface rounded-xl border border-dashed border-gray-200">
                        Belum ada dokumen yang diunggah untuk pemasok ini.
                    </div>
                @endforelse
            </div>

            <!-- Form Upload Dokumen Baru -->
            <div class="mt-6 pt-6 border-t border-gray-100 bg-surface -mx-6 -mb-6 p-6 sm:-mx-8 sm:-mb-8 sm:p-8 rounded-b-2xl">
                <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <x-heroicon-o-arrow-up-tray class="w-4 h-4 text-gray-700" />
                    <span>Unggah Dokumen / Sertifikasi Baru</span>
                </h4>

                <form method="POST" action="{{ route('admin.suppliers.documents.store', $supplier) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Judul Dokumen *</label>
                            <input type="text" name="title" required placeholder="Contoh: Company Profile & ISO 9001:2015" class="w-full text-xs rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Format Tipe Dokumen</label>
                            <select name="type" class="w-full text-xs rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">
                                <option value="PDF">PDF (Dokumen Standar)</option>
                                <option value="DOCX">DOCX (Spesifikasi Word)</option>
                                <option value="XLSX">XLSX (Daftar Harga & Ukuran)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Berkas File (PDF/DOC, maks 15MB) *</label>
                            <input type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx" class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800 transition" />
                        </div>

                        <div class="pt-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_premium" value="1" class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black" />
                                <span class="text-xs font-bold text-gray-800">Kunci Dokumen (Hanya Akun Premium yang Bisa Mengunduh)</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-5 py-2 rounded-xl bg-black hover:bg-gray-800 text-white font-semibold text-xs transition shadow-xs inline-flex items-center gap-1.5">
                            <x-heroicon-o-cloud-arrow-up class="w-4 h-4" />
                            <span>Unggah Dokumen</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

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
                        ★ {{ $supplier->average_rating ? number_format($supplier->average_rating, 1) : '0.0' }} ({{ $supplier->reviews->count() }} ulasan)
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
</x-app-layout>
