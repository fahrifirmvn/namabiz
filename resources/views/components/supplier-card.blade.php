@props(['supplier'])

@php
    $freeDocsCount = $supplier->documents->where('is_premium', false)->count();
    $premiumDocsCount = $supplier->documents->where('is_premium', true)->count();
@endphp

<div class="bg-white rounded-xl border border-gray-200 p-6 relative hover:shadow-md transition-shadow">
    <form method="POST" action="{{ route('suppliers.save', $supplier) }}" class="absolute top-4 right-4">
        @csrf
        <button type="submit" class="text-gray-400 hover:text-gray-700">
            <x-heroicon-o-bookmark class="w-5 h-5" />
        </button>
    </form>

    <div class="flex items-start gap-4">
        <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
            @if($supplier->logo)
                <img src="{{ $supplier->logo }}" class="rounded-lg" alt="{{ $supplier->name }}" />
            @else
                <x-heroicon-o-building-office class="w-6 h-6 text-gray-400" />
            @endif
        </div>
        <div>
            <div class="flex items-center gap-2">
                <h3 class="text-xl font-bold text-text-primary">{{ $supplier->name }}</h3>
                @if($supplier->is_verified)
                    <x-premium-badge type="verified" />
                @endif
            </div>
            <p class="text-text-secondary">{{ $supplier->industry_category }}</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 mt-4">
        @foreach($supplier->tags as $tag)
            <span class="text-xs bg-gray-100 rounded-full px-3 py-1">{{ $tag->tag_name }}</span>
        @endforeach
    </div>

    <div class="bg-gray-50 rounded-lg p-4 mt-4 text-sm space-y-2">
        <div class="flex items-center gap-2 text-gray-700">
            <x-heroicon-o-check class="w-4 h-4 text-green-500" /> Basic company profile
        </div>
        @if($freeDocsCount)
            <div class="flex items-center gap-2 text-gray-700">
                <x-heroicon-o-check class="w-4 h-4 text-green-500" /> Selected brochure
            </div>
        @endif
        @if($premiumDocsCount)
            <div class="flex items-center gap-2 text-gray-400">
                <x-heroicon-o-lock-closed class="w-4 h-4" /> {{ $premiumDocsCount }} additional documents
            </div>
        @endif
    </div>

    <div class="flex gap-3 mt-4">
        <a href="{{ route('suppliers.show', $supplier->slug) }}" class="flex-1 bg-black text-white rounded-lg py-2.5 text-center font-medium hover:bg-gray-800">View Profile &rarr;</a>
        <a href="#" class="flex-1 border border-gray-300 rounded-lg py-2.5 text-center font-medium hover:bg-gray-50">Contact</a>
    </div>
</div>
