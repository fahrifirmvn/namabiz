<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.suppliers.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to List</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Supplier: <span class="text-emerald-700">{{ $supplier->name }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-10 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" enctype="multipart/form-data" class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 space-y-6">
            @csrf
            @method('PUT')

            <!-- Logo Section -->
            <div class="p-6 bg-emerald-50/50 rounded-xl border border-emerald-100 space-y-4">
                <h3 class="text-md font-bold text-gray-900">Supplier Logo & Media</h3>
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 rounded-xl overflow-hidden border bg-white flex items-center justify-center shrink-0">
                        @if($supplier->logo)
                            <img src="{{ str_starts_with($supplier->logo, 'http') ? $supplier->logo : asset('storage/' . $supplier->logo) }}" alt="{{ $supplier->name }}" class="w-full h-full object-cover" />
                        @else
                            <span class="text-emerald-800 font-bold text-lg">{{ strtoupper(substr($supplier->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Upload New Logo File</label>
                            <input type="file" name="logo_file" accept="image/*" class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Or Direct Logo Image URL</label>
                            <input type="text" name="logo_url" value="{{ old('logo_url', str_starts_with($supplier->logo ?? '', 'http') ? $supplier->logo : '') }}" placeholder="https://example.com/logo.png" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Company / Supplier Name *</label>
                    <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Industry Category *</label>
                    <select name="industry_category" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach(['Packaging Manufacturer', 'Distributor & Converter', 'Contract Manufacturer', 'Raw Material Supplier', 'Spices & Coconut'] as $cat)
                            <option value="{{ $cat }}" {{ (old('industry_category', $supplier->industry_category) == $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Country *</label>
                    <input type="text" name="country" value="{{ old('country', $supplier->country) }}" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">City / Plant Location</label>
                    <input type="text" name="city" value="{{ old('city', $supplier->city) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                </div>
            </div>

            <!-- Tagline & Description -->
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tagline</label>
                    <input type="text" name="tagline" value="{{ old('tagline', $supplier->tagline) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Description / About</label>
                    <textarea name="description" rows="3" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $supplier->description) }}</textarea>
                </div>
            </div>

            <!-- Trade & Operational Specs -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-gray-100">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Minimal Order / Min. Pesanan (MOQ)</label>
                    <input type="text" name="moq" value="{{ old('moq', $supplier->moq) }}" placeholder="1 Kontainer 20ft (±10 Ton)" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kapasitas Produksi Bulanan</label>
                    <input type="text" name="production_capacity" value="{{ old('production_capacity', $supplier->production_capacity) }}" placeholder="500 Ton / bulan" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Waktu Siap Kirim / Proses</label>
                    <input type="text" name="avg_lead_time" value="{{ old('avg_lead_time', $supplier->avg_lead_time) }}" placeholder="14 Hari Kerja" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                </div>
            </div>

            <!-- Verification & Checkbox -->
            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" id="is_verified" name="is_verified" value="1" {{ old('is_verified', $supplier->is_verified) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4" />
                <label for="is_verified" class="text-sm font-semibold text-gray-800">Verified Syndicate Supplier (Badge)</label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.suppliers.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition text-sm">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-700 text-white font-semibold rounded-lg hover:bg-emerald-800 transition text-sm shadow-sm">
                    Save Changes & Broadcast to Mobile
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
