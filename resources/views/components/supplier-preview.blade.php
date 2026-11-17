@props(['supplier'])
<div {{ $attributes->merge(['class' => 'nb-elevated p-5 sm:p-6 space-y-5']) }}>
    <div class="flex items-start gap-3">
        <x-ui.avatar size="md" :name="$supplier->name" :logo="$supplier->logo" />
        <div class="min-w-0 space-y-1">
            <h3 class="text-lg font-semibold break-words">{{ $supplier->name }}</h3>
            @if($supplier->is_verified)<x-ui.verified />@endif
            <p class="text-13 text-text-muted">{{ $supplier->city }}{{ $supplier->city ? ', ' : '' }}{{ $supplier->country }}</p>
        </div>
    </div>
    <dl class="grid grid-cols-3 gap-2 rounded-xl bg-surface p-3">
        @foreach([
            'Kapasitas' => \App\Support\SupplierFormat::capacity($supplier->production_capacity),
            'MOQ' => \App\Support\SupplierFormat::moq($supplier->moq),
            'Lead time' => \App\Support\SupplierFormat::leadTime($supplier->avg_lead_time),
        ] as $label => $value)
            <div class="min-w-0">
                <dt class="text-xs text-text-muted">{{ $label }}</dt>
                <dd class="mt-1 text-sm font-semibold">{{ $value }}</dd>
            </div>
        @endforeach
    </dl>
    <div class="flex flex-wrap gap-2">
        @forelse($supplier->certifications->where('status', 'verified') as $certification)
            <span class="nb-chip text-brand-700"><x-heroicon-o-check class="mr-1 h-4 w-4" />{{ $certification->certification?->name }}</span>
        @empty
            <p class="text-sm text-text-muted">Belum ada sertifikasi tervalidasi.</p>
        @endforelse
    </div>
</div>
