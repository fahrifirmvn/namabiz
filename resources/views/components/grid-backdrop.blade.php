@props(['variant' => 'light'])

@php
    $isDark = $variant === 'dark';
    $viewBox = $isDark ? '0 0 720 960' : '0 0 1440 720';
    $preserve = $isDark ? 'xMidYMid slice' : 'xMidYMin slice';
    $patternId = $isDark ? 'nb-grid-dark' : 'nb-grid-light';
    $gridStroke = $isDark ? '#CBD5E1' : '#0F172A';
    $gridOpacity = $isDark ? '0.10' : '0.06';
    $accentHex = $isDark ? '#10B981' : '#059669';
    $secondaryHex = $isDark ? '#94A3B8' : '#334155';

    $cells = $isDark
        ? [
            ['x' => 480, 'y' => 96, 'accent' => true, 'delay' => '0s', 'dur' => '6s'],
            ['x' => 528, 'y' => 144, 'accent' => false, 'delay' => '1.4s', 'dur' => '7s'],
            ['x' => 624, 'y' => 96, 'accent' => false, 'delay' => '2.9s', 'dur' => '6.5s'],
            ['x' => 624, 'y' => 384, 'accent' => true, 'delay' => '2.1s', 'dur' => '6.4s'],
            ['x' => 576, 'y' => 432, 'accent' => false, 'delay' => '3.6s', 'dur' => '7.4s'],
            ['x' => 48, 'y' => 672, 'accent' => false, 'delay' => '4.2s', 'dur' => '7s'],
            ['x' => 96, 'y' => 720, 'accent' => true, 'delay' => '1.1s', 'dur' => '6.6s'],
            ['x' => 432, 'y' => 816, 'accent' => true, 'delay' => '2.6s', 'dur' => '6.8s'],
            ['x' => 480, 'y' => 864, 'accent' => false, 'delay' => '4.8s', 'dur' => '6.2s'],
        ]
        : [
            ['x' => 96, 'y' => 96, 'accent' => true, 'delay' => '0s', 'dur' => '5.8s'],
            ['x' => 144, 'y' => 144, 'accent' => false, 'delay' => '1.3s', 'dur' => '6.6s'],
            ['x' => 240, 'y' => 96, 'accent' => false, 'delay' => '2.6s', 'dur' => '6.2s'],
            ['x' => 96, 'y' => 288, 'accent' => false, 'delay' => '1.9s', 'dur' => '6.8s'],
            ['x' => 192, 'y' => 384, 'accent' => true, 'delay' => '3.1s', 'dur' => '6s'],
            ['x' => 144, 'y' => 480, 'accent' => false, 'delay' => '4.4s', 'dur' => '7s'],
            ['x' => 1248, 'y' => 96, 'accent' => false, 'delay' => '0.8s', 'dur' => '6.4s'],
            ['x' => 1296, 'y' => 144, 'accent' => true, 'delay' => '2.1s', 'dur' => '6s'],
            ['x' => 1152, 'y' => 288, 'accent' => false, 'delay' => '3.5s', 'dur' => '6.8s'],
            ['x' => 1248, 'y' => 384, 'accent' => true, 'delay' => '1.4s', 'dur' => '6.2s'],
            ['x' => 1296, 'y' => 432, 'accent' => false, 'delay' => '4.7s', 'dur' => '6.5s'],
            ['x' => 1152, 'y' => 528, 'accent' => false, 'delay' => '2.9s', 'dur' => '7.2s'],
        ];
@endphp

<div {{ $attributes->merge(['class' => 'pointer-events-none absolute inset-0 overflow-hidden']) }} aria-hidden="true">
    <svg class="h-full w-full" viewBox="{{ $viewBox }}" preserveAspectRatio="{{ $preserve }}" fill="none">
        <defs>
            <pattern id="{{ $patternId }}" width="48" height="48" patternUnits="userSpaceOnUse">
                <path d="M 48 0 L 0 0 0 48" stroke="{{ $gridStroke }}" stroke-opacity="{{ $gridOpacity }}" stroke-width="1" />
            </pattern>
        </defs>

        <rect width="100%" height="100%" fill="url(#{{ $patternId }})" />

        @foreach($cells as $cell)
            @php($color = $cell['accent'] ? $accentHex : $secondaryHex)
            <rect
                x="{{ $cell['x'] }}"
                y="{{ $cell['y'] }}"
                width="48"
                height="48"
                fill="{{ $color }}"
                fill-opacity="{{ $isDark ? '0.14' : '0.09' }}"
                stroke="{{ $color }}"
                stroke-opacity="{{ $isDark ? '0.38' : '0.24' }}"
                stroke-width="1"
                class="nb-cell-pulse"
                style="animation-delay: {{ $cell['delay'] }}; animation-duration: {{ $cell['dur'] }};"
            />
        @endforeach
    </svg>
</div>
