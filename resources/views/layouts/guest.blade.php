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
        <link rel="preload" as="style" href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-text-primary antialiased bg-surface">
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">
            <!-- Panel Value Proposition (Desktop) -->
            <div class="hidden lg:flex lg:col-span-5 relative overflow-hidden bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 text-white p-10 xl:p-12 flex-col justify-between border-r border-white/10">
                <!-- Faint Dot Grid Pattern & Subtle Green Glow -->
                <div class="pointer-events-none absolute inset-0 opacity-20"
                     style="background-image: radial-gradient(rgba(255,255,255,0.22) 1px, transparent 1px); background-size: 24px 24px;"
                     aria-hidden="true"></div>
                <div class="pointer-events-none absolute -top-24 -left-24 w-96 h-96 rounded-full bg-brand-500/15 blur-3xl" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-28 right-0 w-80 h-80 rounded-full bg-brand-500/10 blur-3xl" aria-hidden="true"></div>

                <!-- Top: Brand Logo -->
                <div class="relative z-10 flex items-center justify-between">
                    <a href="/" wire:navigate class="inline-flex items-center gap-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 rounded-xl">
                        <span class="w-10 h-10 rounded-xl bg-white text-gray-950 font-extrabold text-sm flex items-center justify-center shadow-sm">NB</span>
                        <div>
                            <span class="font-bold text-lg tracking-tight block leading-none text-white">{{ config('app.name', 'Namabiz') }}</span>
                            <span class="text-xs text-gray-300 mt-0.5 block">Direktori Pemasok &amp; Pabrik B2B</span>
                        </div>
                    </a>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/10 text-xs font-medium text-brand-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                        <span>12.400+ pemasok &middot; 38 kategori</span>
                    </span>
                </div>

                <!-- Center: Benefit Copy & Pure HTML/CSS Supplier Visual -->
                <div class="relative z-10 my-auto py-8 space-y-6">
                    <div class="space-y-3">
                        <h1 class="text-2xl xl:text-3xl font-extrabold leading-tight tracking-tight text-white">
                            Temukan pabrik yang pas untuk bisnismu, lebih cepat.
                        </h1>
                        <p class="text-sm text-gray-300 leading-relaxed">
                            Bandingkan minimum order, kapasitas, dan waktu produksi dari ribuan pemasok, lalu chat langsung lewat WhatsApp.
                        </p>
                    </div>

                    <ul class="space-y-2.5 text-sm text-gray-200">
                        <li class="flex items-center gap-2.5">
                            <x-heroicon-s-check-circle class="w-5 h-5 text-brand-500 shrink-0" />
                            <span>Simpan pemasok favorit di satu tempat</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <x-heroicon-s-check-circle class="w-5 h-5 text-brand-500 shrink-0" />
                            <span>Unduh katalog dan brosur dalam sekali klik</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <x-heroicon-s-check-circle class="w-5 h-5 text-brand-500 shrink-0" />
                            <span>Hubungi tim sales pabrik langsung, tanpa perantara</span>
                        </li>
                    </ul>

                    <!-- Pure HTML/CSS Mock Supplier Comparison Preview Card -->
                    <div class="rounded-2xl bg-white/[0.06] backdrop-blur-sm border border-white/10 p-4 space-y-3 shadow-lg" aria-hidden="true">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-brand-500/20 border border-brand-500/30 text-brand-200 font-semibold text-xs flex items-center justify-center shrink-0">
                                    AP
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">Atlas Packaging Indonesia</p>
                                    <p class="text-xs text-gray-300 truncate">Packaging Manufacturer &middot; Bekasi, Indonesia</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-brand-500/20 border border-brand-500/30 text-brand-200 text-xs font-medium shrink-0">
                                &#10003; Ditambahkan
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-white/10 text-xs">
                            <div class="bg-white/[0.04] rounded-lg p-2">
                                <span class="text-gray-300 block">MOQ</span>
                                <span class="text-white font-semibold mt-0.5 block">5.000 unit</span>
                            </div>
                            <div class="bg-white/[0.04] rounded-lg p-2">
                                <span class="text-gray-300 block">Lead time</span>
                                <span class="text-white font-semibold mt-0.5 block">2&ndash;3 minggu</span>
                            </div>
                            <div class="bg-white/[0.04] rounded-lg p-2">
                                <span class="text-gray-300 block">Sertifikasi</span>
                                <span class="text-brand-200 font-semibold mt-0.5 block">ISO 9001</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom: Footer & Social Proof -->
                <div class="relative z-10 pt-4 border-t border-white/10 text-xs text-gray-300 flex items-center justify-between">
                    <span>&copy; {{ date('Y') }} {{ config('app.name', 'Namabiz') }}</span>
                    <a href="{{ route('discover.index') }}" wire:navigate class="text-gray-200 hover:text-white underline underline-offset-4 nb-focus-ring rounded">
                        Jelajahi Direktori &rarr;
                    </a>
                </div>
            </div>

            <!-- Panel Form (Mobile Header + Centered Form Card) -->
            <div class="lg:col-span-7 flex flex-col justify-between lg:justify-center items-center px-4 py-6 sm:px-8 sm:py-10">
                <!-- Compact Mobile Header (Logo + 1-line Value Prop) -->
                <div class="w-full max-w-[460px] lg:hidden mb-6 pb-4 border-b border-border flex items-center justify-between gap-3">
                    <a href="/" wire:navigate class="inline-flex items-center gap-2.5 nb-focus-ring rounded-lg">
                        <span class="w-8 h-8 rounded-xl bg-gray-900 text-white font-extrabold text-xs flex items-center justify-center">NB</span>
                        <div>
                            <span class="font-bold text-base text-text-primary block leading-none">{{ config('app.name', 'Namabiz') }}</span>
                            <span class="text-xs text-text-secondary mt-0.5 block">Bandingkan pabrik &amp; hubungi langsung via WhatsApp</span>
                        </div>
                    </a>
                    <span class="text-xs font-medium text-brand-700 bg-brand-50 border border-brand-200 px-2.5 py-1 rounded-full shrink-0">
                        12.400+ pabrik
                    </span>
                </div>

                <!-- Form Card (440-480px width, 32-40px padding) -->
                <div class="w-full max-w-[460px] p-8 sm:p-10 bg-white border border-border shadow-card rounded-2xl">
                    {{ $slot }}
                </div>

                <div class="mt-6 text-center">
                    <a href="/" wire:navigate class="text-13 text-text-secondary hover:text-text-primary inline-flex items-center gap-1.5 transition nb-focus-ring rounded-lg px-2 py-1">
                        <x-heroicon-m-arrow-left class="w-4 h-4" />
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
