@props(['document'])

<div class="flex items-center justify-between py-3 {{ $loop->last ?? true ? '' : 'border-b border-gray-100' }}">
    <div class="flex items-center gap-3">
        <x-heroicon-o-document-text class="w-5 h-5 text-gray-400" />
        <span class="text-sm font-medium {{ $document->is_premium && !auth()->user()?->isPremium() ? 'text-gray-400' : 'text-text-primary' }}">
            {{ $document->title }}
        </span>
    </div>

    @if(!$document->is_premium || auth()->user()?->isPremium())
        <a href="{{ route('documents.download', $document) }}" class="text-gray-500 hover:text-gray-700">
            <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
        </a>
    @else
        <button type="button"
                x-on:click="Livewire.dispatch('open-premium-modal', { documentTitle: '{{ $document->title }}' })"
                class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 border border-gray-300 rounded-lg px-2.5 py-1.5 hover:bg-gray-50">
            <x-heroicon-o-lock-closed class="w-3.5 h-3.5" /> Unlock
        </button>
    @endif
</div>
