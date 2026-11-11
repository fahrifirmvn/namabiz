{{-- Certification checkbox grid. Inputs only; the surrounding form is provided by the caller. --}}
@php
    $assignedCertMap = $supplier->exists ? $supplier->certifications->keyBy('certification_id') : collect();
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($certifications as $cert)
        @php
            $assigned = $assignedCertMap->get($cert->id);
            $oldCert = old("certifications.{$cert->id}");
            $isChecked = is_array($oldCert) ? ! empty($oldCert['active']) : (bool) $assigned;
            $selectedStatus = is_array($oldCert) ? ($oldCert['status'] ?? 'verified') : ($assigned?->status ?? 'verified');
            $isPremium = is_array($oldCert) ? ! empty($oldCert['is_premium_field']) : (bool) $assigned?->is_premium_field;
        @endphp
        <div x-data="{ on: {{ $isChecked ? 'true' : 'false' }} }"
             :class="on ? 'border-emerald-300 bg-emerald-50/20' : 'border-gray-200 bg-surface'"
             class="p-4 rounded-xl border space-y-3 transition">
            <div class="flex items-start justify-between gap-3">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="on" name="certifications[{{ $cert->id }}][active]" value="1" @checked($isChecked) class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black" />
                    <span class="text-xs font-bold text-gray-900">{{ $cert->name }}</span>
                </label>
                <span class="text-2xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-medium">{{ $cert->category }}</span>
            </div>
            <div x-show="on" x-cloak class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 text-xs">
                <div>
                    <label class="block text-2xs font-semibold text-gray-600 mb-1">Status Validasi</label>
                    <select name="certifications[{{ $cert->id }}][status]" class="w-full text-2xs rounded-lg border-gray-300 py-1 px-2 focus:border-black focus:ring-black">
                        <option value="verified" @selected($selectedStatus === 'verified')>Terverifikasi Resmi</option>
                        <option value="partial" @selected($selectedStatus === 'partial')>Dalam Proses Audit</option>
                        <option value="not_verified" @selected($selectedStatus === 'not_verified')>Belum Terverifikasi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-2xs font-semibold text-gray-600 mb-1">Akses Informasi</label>
                    <label class="inline-flex items-center gap-1.5 mt-1 cursor-pointer">
                        <input type="checkbox" name="certifications[{{ $cert->id }}][is_premium_field]" value="1" @checked($isPremium) class="w-3.5 h-3.5 rounded border-gray-300 text-black focus:ring-black" />
                        <span class="text-2xs text-gray-700">Khusus Premium</span>
                    </label>
                </div>
            </div>
        </div>
    @endforeach
</div>
