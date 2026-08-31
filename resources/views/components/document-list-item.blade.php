@props(['document'])

<div class="flex items-center justify-between py-3 {{ $loop->last ?? true ? '' : 'border-b border-gray-100' }}">
    <div class="flex items-center gap-3">
        <x-heroicon-o-document-text aria-hidden="true" class="w-5 h-5 text-gray-400" />
        <span class="text-sm font-medium {{ $document->is_premium && !auth()->user()?->isPremium() ? 'text-gray-600' : 'text-text-primary' }}">
            {{ $document->title }}
        </span>
    </div>

    @if(!$document->is_premium || auth()->user()?->isPremium())
        <a href="{{ route('documents.download', $document) }}" aria-label="Download {{ $document->title }}" class="text-gray-600 hover:text-gray-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2 rounded">
            <x-heroicon-o-arrow-down-tray aria-hidden="true" class="w-5 h-5" />
        </a>
    @else
        <button type="button"
                x-on:click="Livewire.dispatch('open-premium-modal', { documentTitle: @js($document->title) })"
                class="inline-flex items-center gap-1 text-xs font-medium text-gray-600 border border-gray-300 rounded-lg px-2.5 py-1.5 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2">
            <x-heroicon-o-lock-closed aria-hidden="true" class="w-3.5 h-3.5" /> Unlock
        </button>
    @endif
</div>
