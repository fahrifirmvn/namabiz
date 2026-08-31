<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-text-secondary mb-4">
            <a href="{{ route('discover.index') }}" class="hover:text-text-primary">Discover</a>
            <span class="mx-1">&gt;</span>
            <span>{{ $supplier->industry_category }}</span>
            <span class="mx-1">&gt;</span>
            <span class="font-medium text-text-primary">{{ $supplier->name }}</span>
        </nav>

        <div class="flex items-center gap-3 flex-wrap mb-2">
            <h1 class="text-4xl font-bold text-text-primary">{{ $supplier->name }}</h1>
            @if($supplier->is_verified)
                <x-premium-badge :type="auth()->user()?->isPremium() ? 'premium_member' : 'verified'" />
            @endif
        </div>
        <p class="text-text-secondary mb-8">
            <x-heroicon-o-map-pin class="w-4 h-4 inline-block -mt-1" /> {{ $supplier->city }}, {{ $supplier->country }}
            &bull; {{ $supplier->industry_category }}
        </p>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <aside class="lg:col-span-1 space-y-4">
                <div class="bg-white border border-gray-200 rounded-xl p-6 text-center">
                    <div class="w-14 h-14 bg-gray-100 rounded-lg mx-auto flex items-center justify-center mb-3">
                        <x-heroicon-o-building-office class="w-6 h-6 text-gray-400" />
                    </div>
                    <h2 class="font-bold text-lg">{{ $supplier->name }}</h2>
                    <p class="text-text-secondary text-sm mb-4">{{ $supplier->tagline }}</p>

                    @if(filled($supplier->website))
                        @php
                            $websiteUrl = \Illuminate\Support\Str::startsWith($supplier->website, ['http://', 'https://'])
                                ? $supplier->website
                                : 'https://' . $supplier->website;
                        @endphp
                        <a href="{{ $websiteUrl }}" target="_blank" rel="noopener noreferrer" class="block bg-black text-white rounded-lg py-2.5 font-medium text-center hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2">Visit Supplier Website</a>
                    @else
                        <span class="block border border-gray-200 text-gray-500 rounded-lg py-2.5 font-medium text-center">No Website Listed</span>
                    @endif

                    @if(auth()->user()?->isPremium())
                        <form method="POST" action="{{ route('suppliers.save', $supplier) }}" class="mt-2">
                            @csrf
                            <button type="submit" class="w-full border border-gray-300 rounded-lg py-2.5 font-medium hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2">
                                {{ $isSaved ? 'Saved' : 'Save Profile' }}
                            </button>
                        </form>
                    @endif
                </div>
            </aside>

            <div class="lg:col-span-2 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 bg-white border border-gray-200 rounded-xl p-6">
                        <h2 class="font-bold text-lg mb-4 flex items-center gap-2">
                            <x-heroicon-o-information-circle class="w-5 h-5" /> About Supplier
                        </h2>
                        <p class="text-gray-700 leading-relaxed">{{ $supplier->about }}</p>

                        <div class="border-t border-gray-100 mt-4 pt-4">
                            <h3 class="text-xs font-semibold uppercase text-text-secondary mb-2">Core Capabilities</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($supplier->tags as $tag)
                                    <span class="text-sm bg-gray-100 rounded-full px-3 py-1">{{ $tag->tag_name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h2 class="font-bold text-lg mb-4 flex items-center gap-2">
                            <x-heroicon-o-building-office-2 class="w-5 h-5" /> Company Info
                        </h2>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-text-secondary">Country</dt>
                                <dd class="font-medium">{{ $supplier->country }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-text-secondary">Industry</dt>
                                <dd class="font-medium">{{ $supplier->industry_category }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-text-secondary">Website</dt>
                                <dd class="font-medium text-right">
                                    @if(filled($supplier->website))
                                        <a href="{{ $websiteUrl ?? (\Illuminate\Support\Str::startsWith($supplier->website, ['http://', 'https://']) ? $supplier->website : 'https://' . $supplier->website) }}" target="_blank" rel="noopener noreferrer" class="text-gray-900 underline underline-offset-2 hover:text-gray-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black">{{ $supplier->website }}</a>
                                    @else
                                        <span class="text-gray-500">Not listed</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        @if(auth()->user()?->isPremium())
                            <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-gray-100">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-text-secondary uppercase">Founded</p>
                                    <p class="text-2xl font-bold">{{ $supplier->founded_year }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-text-secondary uppercase">Company Size</p>
                                    <p class="text-lg font-bold">{{ $supplier->company_size }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-text-secondary uppercase">Prod. Capacity</p>
                                    <p class="text-lg font-bold">{{ $supplier->production_capacity }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-text-secondary uppercase">MOQ</p>
                                    <p class="text-lg font-bold">{{ $supplier->moq }}</p>
                                </div>
                            </div>
                        @else
                            <dl class="space-y-3 text-sm mt-3 pt-3 border-t border-gray-100">
                                <div class="flex justify-between items-center">
                                    <dt class="text-text-secondary flex items-center gap-1"><x-heroicon-o-lock-closed class="w-3.5 h-3.5" /> Founded</dt>
                                    <dd><x-locked-field /></dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-text-secondary flex items-center gap-1"><x-heroicon-o-lock-closed class="w-3.5 h-3.5" /> Company Size</dt>
                                    <dd><x-locked-field /></dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-text-secondary flex items-center gap-1"><x-heroicon-o-lock-closed class="w-3.5 h-3.5" /> Prod. Capacity</dt>
                                    <dd><x-locked-field /></dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-text-secondary flex items-center gap-1"><x-heroicon-o-lock-closed class="w-3.5 h-3.5" /> MOQ</dt>
                                    <dd><x-locked-field /></dd>
                                </div>
                            </dl>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h2 class="font-bold text-lg mb-4 flex items-center gap-2">
                            <x-heroicon-o-shield-check class="w-5 h-5" /> Certifications
                        </h2>
                        <div class="space-y-3">
                            @forelse($supplier->certifications as $certification)
                                @php $locked = $certification->is_premium_field && !auth()->user()?->isPremium(); @endphp
                                <div class="flex items-center justify-between border border-gray-100 rounded-lg p-3 {{ $locked ? 'opacity-40' : '' }}">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-check-badge class="w-5 h-5 text-gray-500" />
                                        <div>
                                            <p class="font-medium text-sm">{{ $certification->certification->name }}</p>
                                            <p class="text-xs text-text-secondary">{{ $certification->certification->category }}</p>
                                        </div>
                                    </div>
                                    @if($locked)
                                        <span class="text-xs font-medium bg-gray-100 rounded-full px-2 py-1">🔒 PREMIUM</span>
                                    @endif
                                </div>
                            @empty
                                <p class="text-text-secondary text-sm">No certifications listed.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h2 class="font-bold text-lg mb-4 flex items-center gap-2">
                            <x-heroicon-o-folder class="w-5 h-5" /> Documents
                        </h2>
                        <div>
                            @forelse($supplier->documents as $document)
                                <x-document-list-item :document="$document" />
                            @empty
                                <p class="text-text-secondary text-sm">No documents available.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                @unless(auth()->user()?->isPremium())
                    <x-premium-cta-banner :supplier-name="$supplier->name" />
                @endunless
            </div>
        </div>
    </div>
</x-app-layout>
