<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $description ?? 'Masuk atau buat akun Namabiz untuk menyimpan dan membandingkan pemasok pilihan bisnis Anda.' }}">
        <meta name="robots" content="{{ $robots ?? 'noindex, follow' }}">
        <title>{{ isset($title) && $title !== '' ? $title.' — ' : '' }}{{ config('app.name', 'Namabiz') }}</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-text-primary bg-white">
        <a href="#auth-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-4 focus:z-[100] nb-primary">Lewati ke formulir</a>
        <div class="grid min-h-dvh lg:grid-cols-2">
            <aside class="nb-auth-panel hidden lg:flex flex-col p-10 xl:p-16">
                <x-grid-backdrop variant="dark" />
                <a href="{{ route('home') }}" class="relative inline-flex self-start items-center min-h-[44px] rounded-xl bg-white p-2.5" aria-label="Namabiz — beranda"><x-application-logo /></a>
                <div class="relative my-auto py-12 space-y-6">
                    <p class="text-xs font-semibold tracking-[0.08em] text-emerald-200">MITRA TEPAT. BISNIS BERTUMBUH.</p>
                    <h2 class="text-4xl font-bold leading-[42px] tracking-tight text-white" data-hero-step>Temukan pemasok yang pas untuk bisnis Anda.</h2>
                    <p class="text-base leading-[26px] text-slate-300" data-hero-step>{{ \App\Support\SupplierFormat::number($catalogStats['suppliers']) }} pemasok terdaftar di {{ $catalogStats['categories'] }} kategori aktif. Jelajahi, simpan, dan bandingkan pilihan Anda dalam satu tempat.</p>
                    @if($previewSuppliers->first())<x-supplier-preview :supplier="$previewSuppliers->first()" />@endif
                    <p class="text-sm text-slate-300">Data pratinjau diambil dari katalog yang sama.</p>
                </div>
                <p class="relative text-13 text-slate-300">&copy; {{ now()->year }} Namabiz · Direktori B2B Indonesia</p>
            </aside>
            <div class="flex min-w-0 flex-col px-4 py-6 md:px-6 lg:justify-center lg:p-12" style="padding-bottom: max(24px, env(safe-area-inset-bottom))">
                <header class="mb-10 flex items-center justify-between lg:hidden">
                    <a href="{{ route('home') }}" class="nb-icon-button" aria-label="Kembali ke beranda"><x-heroicon-o-arrow-left class="h-5 w-5" /></a>
                    <a href="{{ route('home') }}" class="inline-flex min-h-[44px] items-center" aria-label="Namabiz — beranda"><x-application-logo /></a>
                </header>
                <main id="auth-content" class="w-full max-w-[440px] mx-auto" tabindex="-1">{{ $slot }}</main>
                <a href="{{ route('discover.index') }}" class="nb-ghost mt-8 mx-auto text-sm">Jelajahi tanpa masuk →</a>
            </div>
        </div>
    </body>
</html>
