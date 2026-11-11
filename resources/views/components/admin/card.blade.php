@props(['title', 'description' => null])

<div {{ $attributes->class('bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-xs space-y-6') }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-gray-900">{{ $title }}</h3>
            @if($description)
                <p class="text-xs text-gray-500 mt-0.5">{{ $description }}</p>
            @endif
        </div>
        @isset($action)
            <div class="shrink-0">{{ $action }}</div>
        @endisset
    </div>

    {{ $slot }}
</div>
