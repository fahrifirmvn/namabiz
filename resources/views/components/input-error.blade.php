@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-700 space-y-1', 'aria-live' => 'polite']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-start gap-1.5"><x-heroicon-o-exclamation-circle class="h-4 w-4 shrink-0 mt-0.5" aria-hidden="true" />{{ $message }}</li>
        @endforeach
    </ul>
@endif
