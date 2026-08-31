@if($isLocked)
    <span class="inline-flex items-center gap-1 text-gray-600 text-sm">
        <x-heroicon-o-lock-closed aria-hidden="true" class="w-3.5 h-3.5" />
        Premium
    </span>
@else
    <span class="text-gray-900 font-medium">{{ $value }}</span>
@endif
