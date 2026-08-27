@props(['type' => 'verified'])

@if($type === 'verified')
    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 rounded-full px-3 py-1 text-xs font-medium">
        <x-heroicon-o-check-badge class="w-3.5 h-3.5" /> VERIFIED
    </span>
@elseif($type === 'premium_member')
    <span class="inline-flex items-center gap-1 bg-gray-900 text-white rounded-full px-3 py-1 text-xs font-medium">
        <x-heroicon-o-shield-check class="w-3.5 h-3.5" /> PREMIUM MEMBER
    </span>
@endif
