{{-- Shared by create and edit. Expects Alpine scope with `tab` ('all' shows every section). --}}
@php
    $initialTags = old('tags') !== null
        ? array_values(array_filter(array_map('trim', explode(',', old('tags')))))
        : ($supplier->exists ? $supplier->tags->pluck('tag_name')->all() : []);
@endphp

<!-- Profil: identitas, logo, narasi -->
<div x-show="tab === 'all' || tab === 'profil'" class="space-y-6">
    <x-admin.card title="Identitas & Logo" description="Nama usaha, kategori, dan representasi visual.">
        @if($supplier->exists)
            <x-slot name="action">
                <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700 font-mono text-2xs">{{ $supplier->slug }}</code>
            </x-slot>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-admin.field class="md:col-span-2" label="Nama Perusahaan / Pabrik" name="name" :value="$supplier->name" :required="true" placeholder="Contoh: PT Sumber Plastik Lestari" />

            <div>
                <label for="industry_category" class="block text-xs font-bold text-gray-700 mb-1">Kategori Industri <span class="text-red-500">*</span></label>
                @php
                    $selectedCategory = old('industry_category', $supplier->industry_category);
                    $categoryNames = $categories->pluck('name');
                @endphp
                <select id="industry_category" name="industry_category" required class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">
                    @if($categories->isEmpty())
                        <option value="">Pilih kategori...</option>
                    @endif
                    @if($selectedCategory && ! $categoryNames->contains($selectedCategory))
                        <option value="{{ $selectedCategory }}" selected>{{ $selectedCategory }}</option>
                    @endif
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}" @selected($selectedCategory === $cat->name)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('industry_category') <p class="text-2xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <x-admin.field label="Tagline Singkat" name="tagline" :value="$supplier->tagline" placeholder="Contoh: Produsen kemasan fleksibel ramah lingkungan" />
        </div>

        <div class="p-5 bg-surface rounded-xl border border-gray-200 flex flex-col sm:flex-row items-start gap-5"
             x-data="{ preview: @js($supplier->logo ? (str_starts_with($supplier->logo, 'http') ? $supplier->logo : asset('storage/'.$supplier->logo)) : null) }">
            <div class="w-20 h-20 rounded-2xl overflow-hidden border border-gray-200 bg-white flex items-center justify-center shrink-0">
                <template x-if="preview"><img :src="preview" alt="Pratinjau logo" class="w-full h-full object-cover"></template>
                <template x-if="!preview"><x-heroicon-o-photo class="w-7 h-7 text-gray-300" /></template>
            </div>
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Unggah Logo (PNG/JPG, maks 2MB)</label>
                    <input type="file" name="logo_file" accept="image/*"
                           @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview"
                           class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800 transition" />
                    @error('logo_file') <p class="text-2xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <x-admin.field label="Atau tautan URL gambar" name="logo_url" type="url" :value="str_starts_with($supplier->logo ?? '', 'http') ? $supplier->logo : ''" placeholder="https://example.com/logo.png" />
                @if($supplier->exists && $supplier->logo)
                    <label class="inline-flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remove_logo" value="1" class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black"> Hapus logo saat ini
                    </label>
                @endif
            </div>
        </div>
    </x-admin.card>

    <x-admin.card title="Profil & Narasi" description="Latar belakang dan keunggulan yang tampil di profil publik.">
        <div class="space-y-4">
            <div>
                <label for="about" class="block text-xs font-bold text-gray-700 mb-1">Tentang Perusahaan</label>
                <textarea id="about" name="about" rows="3" placeholder="Sejarah dan profil singkat perusahaan..." class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">{{ old('about', $supplier->about) }}</textarea>
            </div>
            <div>
                <label for="description" class="block text-xs font-bold text-gray-700 mb-1">Keunggulan / Deskripsi Singkat</label>
                <textarea id="description" name="description" rows="3" placeholder="Keunggulan fasilitas produksi, sertifikasi higienis..." class="w-full text-sm rounded-xl border-gray-300 shadow-xs focus:border-black focus:ring-black">{{ old('description', $supplier->description) }}</textarea>
            </div>
        </div>
    </x-admin.card>
</div>

<!-- Kontak & Lokasi -->
<div x-show="tab === 'all' || tab === 'kontak'" class="space-y-6">
    <x-admin.card title="Kontak Langsung & Lokasi" description="Pembeli menghubungi pemasok langsung lewat WhatsApp dan email.">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-admin.field label="Nomor WhatsApp" name="whatsapp" :value="$supplier->whatsapp" placeholder="081234567890" hint="Otomatis diformat ke tautan wa.me/62..." />
            <x-admin.field label="Email Resmi" name="email" type="email" :value="$supplier->email" placeholder="sales@perusahaan.co.id" />
            <x-admin.field label="Website" name="website" :value="$supplier->website" placeholder="https://perusahaan.co.id" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
            <x-admin.field label="Negara" name="country" :value="$supplier->country" :required="true" />
            <x-admin.field label="Kota / Lokasi Pabrik" name="city" :value="$supplier->city" placeholder="Contoh: Surabaya, Jawa Timur" />
        </div>
        @if($supplier->exists && $supplier->whatsapp_url)
            <a href="{{ $supplier->whatsapp_url }}" target="_blank" rel="noopener" class="text-2xs text-emerald-700 hover:underline">Uji buka chat WhatsApp &rarr;</a>
        @endif
    </x-admin.card>
</div>

<!-- Operasional, tag, status -->
<div x-show="tab === 'all' || tab === 'operasional'" class="space-y-6">
    <x-admin.card title="Spesifikasi Operasional" description="Data yang dipakai pembeli untuk membandingkan pemasok.">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-admin.field label="MOQ (Pesanan Min.)" name="moq" :value="$supplier->moq" placeholder="10.000 unit" />
            <x-admin.field label="Kapasitas Produksi" name="production_capacity" :value="$supplier->production_capacity" placeholder="500.000 unit / bulan" />
            <x-admin.field label="Lead Time Standar" name="avg_lead_time" :value="$supplier->avg_lead_time" placeholder="2-3 minggu" />
            <x-admin.field label="Ukuran Perusahaan" name="company_size" :value="$supplier->company_size" placeholder="250 - 500 Karyawan" />
            <x-admin.field label="Tahun Berdiri" name="founded_year" type="number" :value="$supplier->founded_year" placeholder="2012" />
            <x-admin.field label="Jejak Karbon / ISO Status" name="carbon_footprint_report" :value="$supplier->carbon_footprint_report" placeholder="Terakreditasi ISO 14001" />
        </div>
    </x-admin.card>

    <x-admin.card title="Kemampuan Inti (Tag)" description="Ketik lalu tekan Enter atau koma. Tampil sebagai label di profil dan kartu pemasok.">
        <div x-data="{
                tags: @js($initialTags),
                input: '',
                add() {
                    this.input.split(',').map(t => t.trim()).filter(Boolean).forEach(t => {
                        if (this.tags.length < 20 && !this.tags.some(x => x.toLowerCase() === t.toLowerCase())) this.tags.push(t);
                    });
                    this.input = '';
                }
            }">
            <input type="hidden" name="tags" :value="tags.join(',')">
            <div class="flex flex-wrap gap-2 p-3 rounded-xl border border-gray-300 bg-white focus-within:border-black focus-within:ring-1 focus-within:ring-black">
                <template x-for="(tag, i) in tags" :key="tag">
                    <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded-lg">
                        <span x-text="tag"></span>
                        <button type="button" @click="tags.splice(i, 1)" class="text-gray-400 hover:text-red-600" aria-label="Hapus tag">&times;</button>
                    </span>
                </template>
                <input type="text" x-model="input"
                       @keydown.enter.prevent="add()" @keydown.comma.prevent="add()" @blur="add()"
                       placeholder="Contoh: Standing pouch, Rotogravure..."
                       class="flex-1 min-w-[10rem] border-0 p-0 text-sm focus:ring-0 placeholder-gray-400">
            </div>
            <p class="text-2xs text-gray-400 mt-1"><span x-text="tags.length"></span>/20 tag</p>
        </div>
    </x-admin.card>

    <x-admin.card title="Status Verifikasi">
        <label class="inline-flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="is_verified" value="1" @checked(old('is_verified', $supplier->is_verified)) class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black" />
            <span class="text-sm font-bold text-gray-900">Tandai sebagai Pemasok Terverifikasi Namabiz</span>
        </label>
    </x-admin.card>
</div>
