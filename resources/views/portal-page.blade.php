<x-app-layout
    :title="$content['title']"
    :description="$content['summary']"
    :canonical="\App\Support\PortalContent::pageUrl($page)"
>
    <section class="nb-section relative overflow-hidden bg-white">
        <div class="pointer-events-none absolute inset-0 nb-grid-bg nb-grid-mask" aria-hidden="true"></div>
        <div class="nb-container relative">
            <nav aria-label="Jejak halaman" class="flex items-center gap-2 text-sm text-text-muted"><a href="{{ route('home') }}" class="inline-flex min-h-[44px] items-center hover:text-brand-700">Beranda</a><span aria-hidden="true">/</span><span>{{ $content['title'] }}</span></nav>
            <p class="nb-eyebrow mt-6">INFORMASI NAMABIZ</p>
            <h1 class="nb-h1 mt-3">{{ $content['title'] }}</h1>
            <p class="mt-4 max-w-[720px] text-base leading-[26px] text-text-muted">{{ $content['summary'] }}</p>
            <div class="mt-6 max-w-[720px] rounded-xl bg-amber-50 p-4 text-sm text-amber-800" role="note">
                <p class="font-semibold">{{ config('namabiz.draft.label') }}</p>
                <p class="mt-2 leading-[22px]">{{ $content['legal'] ? config('namabiz.draft.notice') : 'Panduan ini masih berupa draf. Konten perlu ditinjau pemilik Namabiz sebelum disahkan.' }}</p>
            </div>
        </div>
    </section>
    <div class="nb-container nb-section grid gap-8 lg:grid-cols-[minmax(0,1fr)_280px]">
        <article class="min-w-0 space-y-8">
            @foreach($content['sections'] as $section)
                <section class="max-w-[720px]" data-reveal>
                    <h2 class="text-xl font-semibold leading-7">{{ $section['title'] }}</h2>
                    <p class="mt-3 text-base leading-[26px] text-text-secondary">{{ isset($section['content']) ? config('namabiz.draft.'.$section['content']) : $section['body'] }}</p>
                </section>
            @endforeach
            @if($page === 'contact')<x-portal-contacts class="max-w-[720px] rounded-2xl bg-white p-5" />@endif
            @if(!empty($content['sources']))
                <section class="max-w-[720px] border-t border-border pt-6">
                    <h2 class="text-base font-semibold">Sumber resmi Google Play</h2>
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach($content['sources'] as $source)
                            <li><a href="{{ config('namabiz.links.'.$source) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-[44px] items-center gap-2 text-brand-700 underline underline-offset-4 break-all">{{ ['play_cancellation' => 'Panduan pembatalan langganan', 'play_payments' => 'Metode pembayaran Google Play', 'play_subscriptions' => 'Kelola langganan Google Play'][$source] }}<x-heroicon-o-arrow-up-right class="h-4 w-4 shrink-0" /></a></li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </article>
        <aside class="self-start nb-card p-5 lg:sticky lg:top-24">
            <h2 class="text-sm font-semibold">Informasi lainnya</h2>
            <nav aria-label="Halaman informasi" class="mt-3 grid gap-1">
                @foreach(config('namabiz.pages') as $key => $item)
                    <a href="{{ \App\Support\PortalContent::pageUrl($key) }}" class="nb-ghost justify-start {{ $key === $page ? 'bg-brand-50 !text-brand-700' : '' }}" @if($key === $page) aria-current="page" @endif>{{ $item['title'] }}</a>
                @endforeach
            </nav>
        </aside>
    </div>
</x-app-layout>
