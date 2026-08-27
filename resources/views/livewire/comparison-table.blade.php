<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-start justify-between gap-4 flex-wrap mb-6">
        <div>
            <h1 class="text-3xl font-bold text-text-primary">Compare Suppliers</h1>
            <p class="text-text-secondary mt-1">Evaluate technical specifications and compliance metrics across selected manufacturers.</p>
        </div>

        @if($this->isPremium)
            <a href="{{ route('compare.download', ['suppliers' => $supplierIds]) }}" class="inline-flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-gray-50">
                <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Download Comparison (PDF)
            </a>
        @else
            <button type="button"
                    x-data
                    x-on:click="Livewire.dispatch('open-premium-modal', { documentTitle: 'Comparison PDF' })"
                    class="inline-flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-400 cursor-not-allowed">
                <x-heroicon-o-lock-closed class="w-4 h-4" /> Download Comparison (PDF)
            </button>
        @endif
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left font-medium text-text-secondary p-4 w-48">Metric</th>
                    @foreach($this->suppliers as $supplier)
                        <th class="text-left font-medium p-4 min-w-[200px]">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-lg font-bold text-text-primary">{{ $supplier->name }}</span>
                                <button wire:click="removeSupplier({{ $supplier->id }})" class="text-gray-400 hover:text-gray-600">
                                    <x-heroicon-o-x-mark class="w-4 h-4" />
                                </button>
                            </div>
                        </th>
                    @endforeach
                    @if($this->suppliers->count() < 3)
                        <th class="text-left font-medium p-4 min-w-[200px] relative">
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Add Supplier"
                                   class="w-full rounded-lg border-gray-300 text-sm font-normal">
                            @if($this->searchResults->isNotEmpty())
                                <div class="absolute z-10 left-4 right-4 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg">
                                    @foreach($this->searchResults as $result)
                                        <button wire:click="addSupplier({{ $result->id }})"
                                                class="block w-full text-left px-3 py-2 text-sm font-normal hover:bg-gray-50">
                                            {{ $result->name }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <tr class="bg-gray-50">
                    <td colspan="{{ $this->suppliers->count() + 2 }}" class="px-4 py-2 text-xs font-semibold uppercase text-text-secondary">Basic Information</td>
                </tr>
                <tr class="border-b border-gray-100">
                    <td class="p-4 text-text-secondary">Location</td>
                    @foreach($this->suppliers as $supplier)
                        <td class="p-4">{{ $supplier->city }}, {{ $supplier->country }}</td>
                    @endforeach
                </tr>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <td class="p-4 text-text-secondary">Founded</td>
                    @foreach($this->suppliers as $supplier)
                        <td class="p-4">{{ $supplier->founded_year }}</td>
                    @endforeach
                </tr>
                <tr class="border-b border-gray-100">
                    <td class="p-4 text-text-secondary">Company Size</td>
                    @foreach($this->suppliers as $supplier)
                        <td class="p-4">{{ $supplier->company_size }}</td>
                    @endforeach
                </tr>

                <tr class="bg-gray-50">
                    <td colspan="{{ $this->suppliers->count() + 2 }}" class="px-4 py-2 text-xs font-semibold uppercase text-text-secondary">
                        <span class="inline-flex items-center gap-1">Operational Metrics <x-heroicon-o-lock-closed class="w-3 h-3" /></span>
                    </td>
                </tr>
                @foreach([['label' => 'Production Capacity', 'field' => 'production_capacity'], ['label' => 'MOQ (Minimum Order)', 'field' => 'moq'], ['label' => 'Avg. Lead Time', 'field' => 'avg_lead_time']] as $metric)
                    <tr class="border-b border-gray-100 {{ $loop->even ? 'bg-gray-50/50' : '' }}">
                        <td class="p-4 text-text-secondary">{{ $metric['label'] }}</td>
                        @foreach($this->suppliers as $supplier)
                            <td class="p-4">
                                <x-locked-field :value="$supplier->{$metric['field']}" :force-locked="!$this->isPremium" />
                            </td>
                        @endforeach
                    </tr>
                @endforeach

                <tr class="bg-gray-50">
                    <td colspan="{{ $this->suppliers->count() + 2 }}" class="px-4 py-2 text-xs font-semibold uppercase text-text-secondary">Compliance &amp; Sustainability</td>
                </tr>
                @foreach(['ISO 9001:2015' => 'ISO 9001', 'ISO 14001' => 'ISO 14001'] as $certName => $label)
                    <tr class="border-b border-gray-100 {{ $loop->even ? 'bg-gray-50/50' : '' }}">
                        <td class="p-4 text-text-secondary">{{ $label }}</td>
                        @foreach($this->suppliers as $supplier)
                            @php $cert = $this->certificationFor($supplier, $certName); @endphp
                            <td class="p-4">
                                @if(!$cert)
                                    <span class="text-gray-300">&mdash;</span>
                                @elseif($cert->is_premium_field && !$this->isPremium)
                                    <x-locked-field :force-locked="true" />
                                @elseif($cert->status === 'verified')
                                    <span class="inline-flex items-center gap-1 text-text-primary"><x-heroicon-o-check-circle class="w-4 h-4" /> Verified</span>
                                @elseif($cert->status === 'partial')
                                    <span class="inline-flex items-center gap-1 text-gray-400"><x-heroicon-o-minus-circle class="w-4 h-4" /> Partial</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-gray-400"><x-heroicon-o-x-circle class="w-4 h-4" /> Not Verified</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                <tr class="border-b border-gray-100">
                    <td class="p-4 text-text-secondary">Carbon Footprint Rep.</td>
                    @foreach($this->suppliers as $supplier)
                        <td class="p-4">
                            <x-locked-field :value="$supplier->carbon_footprint_report ?? 'Not Disclosed'" :force-locked="!$this->isPremium" />
                        </td>
                    @endforeach
                </tr>

                <tr class="bg-gray-50">
                    <td colspan="{{ $this->suppliers->count() + 2 }}" class="px-4 py-2 text-xs font-semibold uppercase text-text-secondary">Document Availability</td>
                </tr>
                @foreach(['Product Catalog', 'Audit Report'] as $docTitle)
                    <tr class="border-b border-gray-100 {{ $loop->even ? 'bg-gray-50/50' : '' }}">
                        <td class="p-4 text-text-secondary">{{ $docTitle }}</td>
                        @foreach($this->suppliers as $supplier)
                            @php $doc = $this->documentFor($supplier, $docTitle); @endphp
                            <td class="p-4">
                                @if(!$doc)
                                    <span class="text-gray-300">N/A</span>
                                @elseif($doc->is_premium && !$this->isPremium)
                                    <span class="inline-flex items-center gap-1 text-gray-400"><x-heroicon-o-lock-closed class="w-3.5 h-3.5" /> Premium</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-text-primary"><x-heroicon-o-document-text class="w-4 h-4" /> {{ ucfirst($doc->type) }}</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @unless($this->isPremium)
        <x-premium-cta-banner />
    @endunless
</div>
