<!DOCTYPE html>
<html lang="id">
    <head>
        @php
            $appName = config('app.name', 'Namabiz');
            $metaTitle = isset($title) && $title !== '' ? $title.' — '.$appName : $appName.' — Direktori Pemasok B2B Indonesia';
            $metaDescription = $description ?? 'Jelajahi direktori pemasok B2B Namabiz. Cari, bandingkan, dan hubungi calon mitra untuk kebutuhan bisnis Anda.';
            $metaCanonical = $canonical ?? url()->current();
            $metaOgImage = $ogImage ?? asset('images/og-default.png');
        @endphp
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $metaDescription }}">
        @if(!empty($robots))<meta name="robots" content="{{ $robots }}">@endif
        <link rel="canonical" href="{{ $metaCanonical }}">

        <title>{{ $metaTitle }}</title>

        <!-- Open Graph & Twitter -->
        <meta property="og:site_name" content="{{ $appName }}">
        <meta property="og:type" content="website">
        <meta property="og:locale" content="id_ID">
        <meta property="og:title" content="{{ $metaTitle }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:url" content="{{ $metaCanonical }}">
        <meta property="og:image" content="{{ $metaOgImage }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $metaTitle }}">
        <meta name="twitter:description" content="{{ $metaDescription }}">
        <meta name="twitter:image" content="{{ $metaOgImage }}">

        <!-- Icons -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="preload" as="style" href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body class="font-sans antialiased bg-surface {{ auth()->check() ? 'nb-authenticated' : '' }}">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-4 focus:z-[100] nb-primary">Lewati ke konten</a>
        <div class="min-h-screen flex flex-col">
            <x-navbar />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main id="main-content" class="flex-1 min-w-0" tabindex="-1">
                {{ $slot }}
            </main>

            <x-footer />
            @auth<x-bottom-navigation />@endauth
            <livewire:premium-modal />
        </div>
    </body>
</html>
