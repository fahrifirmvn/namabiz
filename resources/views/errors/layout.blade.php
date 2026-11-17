<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('code') — @yield('title') | {{ config('app.name', 'Namabiz') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css'])
    @endif
    <style>
        body { margin: 0; font-family: Figtree, system-ui, -apple-system, sans-serif; background: #F8FAFC; color: #0F172A; }
        .err-wrap { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; }
        .err-card { width: 100%; max-width: 520px; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 18px; padding: 32px; box-shadow: 0 12px 32px -18px rgba(15, 23, 42, .16); }
        .err-brand { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; color: #0F172A; font-weight: 700; font-size: 18px; }
        .err-badge { display: inline-block; margin-top: 24px; padding: 4px 10px; border-radius: 999px; background: #ECFDF5; color: #047857; font-size: 12px; font-weight: 700; letter-spacing: .04em; }
        .err-title { margin: 12px 0 8px; font-size: 24px; line-height: 1.25; font-weight: 800; color: #0F172A; }
        .err-desc { margin: 0; font-size: 15px; line-height: 1.6; color: #475569; }
        .err-actions { margin-top: 24px; display: flex; flex-wrap: wrap; gap: 12px; }
        .err-btn-primary { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 0 18px; border-radius: 12px; background: #047857; color: #FFFFFF; font-size: 14px; font-weight: 600; text-decoration: none; }
        .err-btn-primary:hover { background: #065F46; }
        .err-btn-secondary { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 0 18px; border-radius: 12px; background: #F8FAFC; border: 1px solid #CBD5E1; color: #0F172A; font-size: 14px; font-weight: 600; text-decoration: none; }
        .err-btn-secondary:hover { background: #F1F5F9; }
    </style>
</head>
<body>
    <main class="err-wrap">
        <div class="err-card">
            <a href="{{ url('/') }}" class="err-brand">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 184 112" width="36" height="22" aria-hidden="true">
                    <path fill="#0F172A" d="M8 8H30L76 54H54L78 92V104H8V8ZM23 36V89H55L23 36Z"/>
                    <path fill="#0F172A" d="M74 8H106V104H91V23H74V8Z"/>
                    <path fill="#047857" fill-rule="evenodd" clip-rule="evenodd" d="M122 8H153C166 8 174 16 174 29C174 38 168 45 159 48C169 51 176 59 176 72C176 90 163 104 144 104H122V8ZM137 22V43H150C156 43 159 39 159 32C159 26 156 22 150 22H137ZM137 56V90H145C155 90 161 83 161 73C161 62 155 56 145 56H137Z"/>
                </svg>
                <span>Namabiz</span>
            </a>
            <div>
                <span class="err-badge">KODE @yield('code')</span>
            </div>
            <h1 class="err-title">@yield('title')</h1>
            <p class="err-desc">@yield('message')</p>
            <div class="err-actions">
                <a href="{{ url('/') }}" class="err-btn-primary">Kembali ke Beranda</a>
                <a href="{{ url('/discover') }}" class="err-btn-secondary">Jelajahi Direktori</a>
            </div>
        </div>
    </main>
</body>
</html>
