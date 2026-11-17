<x-app-layout>
    <section class="nb-section relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-80 nb-grid-bg nb-grid-mask" aria-hidden="true"></div>
        <div class="nb-container relative">
            <p class="nb-eyebrow">PAKET NAMABIZ</p>
            <h1 class="nb-h1 mt-3">Kenali akses Premium.</h1>
            <p class="mt-4 max-w-[720px] text-base leading-[26px] text-text-muted">Pelajari metrik operasional dan dokumen yang tersedia pada profil pemasok sebelum menentukan pilihan.</p>
            @if(session('paywall_triggered'))<p role="status" class="mt-6 rounded-xl bg-amber-50 p-4 text-sm text-amber-800">Fitur atau dokumen tersebut memerlukan akses Premium aktif.</p>@endif
            <div class="mt-8 grid items-start gap-6 md:grid-cols-2">
                <article class="nb-premium-card p-6 md:p-8" data-reveal>
                    <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-900">Akses data lebih lengkap</span>
                    <h2 class="mt-6 text-2xl font-bold text-white">Premium</h2>
                    <x-premium-price class="mt-3" />
                    <ul class="mt-6 mb-8 space-y-4 text-sm">
                        @foreach(['Metrik operasional sesuai data profil', 'Informasi sertifikasi yang tersedia', 'Dokumen sesuai hak akses Premium', 'Ekspor PDF perbandingan pemasok'] as $feature)<li class="flex gap-3 text-white"><x-heroicon-o-check-circle class="h-5 w-5 shrink-0 text-emerald-200" />{{ $feature }}</li>@endforeach
                    </ul>
                    @if(auth()->user()?->isPremium())
                        <p class="mb-5 rounded-xl bg-brand-50 p-4 text-sm text-brand-700" role="status">Akun Anda memiliki akses Premium aktif.@if(auth()->user()->premium_expires_at) Berlaku hingga {{ auth()->user()->premium_expires_at->format('d-m-Y') }}.@endif</p>
                    @endif
                    <x-premium-purchase id="upgrade-premium" :show-price="false" :inverse="true" />
                </article>
                <article class="nb-card p-6 md:p-8">
                    <p class="text-xs font-semibold text-amber-800">{{ config('namabiz.draft.label') }}</p>
                    <h2 class="mt-3 text-xl font-semibold">Pembayaran & langganan</h2>
                    <p class="mt-4 text-sm leading-[22px] text-text-muted">{{ config('namabiz.draft.payments') }}</p>
                    <p class="mt-4 text-sm leading-[22px] text-text-muted">{{ config('namabiz.draft.cancellation') }}</p>
                    <a href="{{ \App\Support\PortalContent::pageUrl('help') }}" class="nb-secondary mt-6 w-full">Baca panduan dan sumber resmi</a>
                </article>
            </div>
        </div>
    </section>
</x-app-layout>
