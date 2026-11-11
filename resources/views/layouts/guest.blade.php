<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' . config('app.name', 'Namabiz') : config('app.name', 'Namabiz') }}</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="preload" as="style" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-surface">
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">
            <!-- Panel Value Proposition (Desktop) -->
            <div class="hidden lg:flex lg:col-span-5 bg-black text-white p-10 xl:p-14 flex-col justify-between">
                <div>
                    <a href="/" wire:navigate class="inline-flex items-center gap-2.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white rounded-lg">
                        <span class="w-9 h-9 rounded-xl bg-white text-black font-extrabold text-sm flex items-center justify-center">NB</span>
                        <div>
                            <span class="font-extrabold text-lg tracking-tight block leading-none">{{ config('app.name', 'Namabiz') }}</span>
                            <span class="text-2xs text-gray-400">Direktori Pemasok B2B Terverifikasi</span>
                        </div>
                    </a>
                </div>

                <div class="space-y-6 my-auto py-12">
                    <span class="inline-flex items-center gap-1.5 text-2xs font-semibold uppercase tracking-wider px-3 py-1 rounded-full bg-white/10 text-gray-200">
                        Platform Sourcing & Katalog Industri
                    </span>
                    <h1 class="text-2xl xl:text-3xl font-extrabold leading-tight">
                        Hubungkan Pengadaan Anda dengan Pabrik &amp; Pemasok Terpercaya.
                    </h1>
                    <p class="text-sm text-gray-300 leading-relaxed">
                        Temukan mitra manufaktur terverifikasi, bandingkan spesifikasi operasional, dan unduh dokumen katalog resmi dalam satu tempat.
                    </p>

                    <ul class="space-y-3 pt-2 text-xs text-gray-200">
                        <li class="flex items-start gap-2.5">
                            <x-heroicon-s-check-circle class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                            <span>Data MOQ, kapasitas produksi, lead time, dan sertifikasi mutu yang transparan.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <x-heroicon-s-check-circle class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                            <span>Akses cepat ke brosur produk, spesifikasi teknis, dan dokumen legalitas.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <x-heroicon-s-check-circle class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                            <span>Hubungi tim sales pabrik secara langsung via WhatsApp maupun email resmi.</span>
                        </li>
                    </ul>
                </div>

                <div class="text-2xs text-gray-400 flex items-center justify-between">
                    <span>&copy; {{ date('Y') }} {{ config('app.name', 'Namabiz') }}</span>
                    <a href="{{ route('discover.index') }}" wire:navigate class="text-gray-300 hover:text-white underline">Jelajahi Direktori &rarr;</a>
                </div>
            </div>

            <!-- Panel Form -->
            <div class="lg:col-span-7 flex flex-col justify-center items-center px-4 py-10 sm:px-8">
                <div class="lg:hidden mb-6">
                    <a href="/" wire:navigate class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black rounded-lg inline-block">
                        <x-application-logo />
                    </a>
                </div>

                <div class="w-full max-w-md px-6 py-8 sm:px-8 bg-white border border-gray-200 shadow-xs rounded-2xl">
                    {{ $slot }}
                </div>

                <div class="mt-6 text-center">
                    <a href="/" wire:navigate class="text-xs text-gray-500 hover:text-gray-900 inline-flex items-center gap-1 transition">
                        <x-heroicon-m-arrow-left class="w-3.5 h-3.5" />
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
