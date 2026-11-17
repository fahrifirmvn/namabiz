<x-app-layout
    title="Direktori Pemasok B2B Indonesia"
    description="Cari, bandingkan, dan hubungi pemasok B2B terkurasi di Indonesia."
    :canonical="route('home')"
>
    @push('head')
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Namabiz',
            'url' => route('home'),
            'logo' => asset('images/logo-namabiz.png'),
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush
    <section class="relative overflow-hidden border-b border-border bg-white pt-14 pb-16 lg:pt-20 lg:pb-24">
        <x-grid-backdrop variant="light" class="nb-grid-mask" />
        <div class="nb-container relative mx-auto max-w-4xl text-center">
            <p class="nb-eyebrow mb-4 inline-block text-brand-700" data-hero-step>DIREKTORI B2B INDONESIA</p>
            <h1 class="nb-h1" data-hero-step>Pemasok tepat,<br><span class="nb-hero-accent text-brand-700">lebih cepat.</span></h1>
            <p class="mx-auto mt-5 max-w-xl text-base leading-[26px] text-text-secondary" data-hero-step>Cari, bandingkan, dan hubungi pemasok untuk bisnis Anda. Mulai dari data.</p>
            <div class="mt-8" data-hero-step>
                <form action="{{ route('discover.index') }}" method="GET" class="nb-search mx-auto w-full !max-w-2xl">
                    <x-heroicon-o-magnifying-glass class="ml-2 h-5 w-5 shrink-0 text-text-muted" aria-hidden="true" />
                    <label for="hero-search" class="sr-only">Cari pemasok atau bahan baku</label>
                    <input id="hero-search" name="q" type="search" placeholder="Cari pemasok atau bahan baku">
                    <button type="submit" class="nb-accent !min-h-[44px] !px-4" aria-label="Cari pemasok"><span class="hidden min-[360px]:inline">Cari</span><x-heroicon-o-arrow-right class="h-5 w-5" /></button>
                </form>
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                    <span class="inline-flex shrink-0 items-center gap-1.5 text-13 text-text-muted"><x-heroicon-o-arrow-trending-up class="h-4 w-4" />Populer:</span>
                    @foreach(['Kemasan', 'Resin', 'Mesin', 'Tekstil', 'Bahan makanan'] as $keyword)
                        <a href="{{ route('discover.index', ['q' => $keyword]) }}" class="nb-chip min-h-[44px] hover:bg-brand-50 hover:text-brand-700">{{ $keyword }}</a>
                    @endforeach
                </div>
                <p class="mt-4 inline-flex items-center justify-center gap-1.5 text-13 text-text-muted"><x-heroicon-o-check-circle class="h-4 w-4 text-brand-700" />Gratis untuk memulai. Tanpa Biaya</p>
            </div>
            <dl class="mx-auto mt-10 grid max-w-2xl grid-cols-1 divide-y divide-border rounded-2xl border border-border bg-surface text-center tabular-nums sm:grid-cols-3 sm:divide-x sm:divide-y-0" data-hero-step>
                @foreach([
                    [$catalogStats['suppliers'], 'Pemasok terdaftar'],
                    [$catalogStats['verified'], 'Terverifikasi'],
                    [$catalogStats['categories'], 'Kategori aktif'],
                ] as [$number, $label])
                    <div class="px-6 py-5">
                        <dt class="text-2xl font-bold text-text-primary" data-count="{{ $number }}">{{ \App\Support\SupplierFormat::number($number) }}</dt>
                        <dd class="mt-1 text-xs font-medium text-text-muted">{{ $label }}</dd>
                    </div>
                @endforeach
            </dl>
            <p class="mt-3 text-xs text-text-muted" data-hero-step>Jumlah aktual dalam katalog</p>
        </div>
    </section>

    <section class="border-b border-border bg-surface py-14 lg:py-20">
        <div class="nb-container">
            <div class="mx-auto max-w-5xl">
                <x-comparison-preview :suppliers="$previewSuppliers" :lowest-moq="$lowestPreviewMoq" />
            </div>
        </div>
    </section>

    <section id="cara-kerja" class="nb-section bg-white">
        <div class="nb-container">
            <p class="nb-eyebrow">CARA KERJA</p>
            <h2 class="nb-h2 mt-2">Dari pencarian ke percakapan.</h2>
            <p class="mt-3 text-base text-text-muted">Tiga langkah untuk mengenali calon mitra bisnis Anda.</p>
            <ol class="mt-8 grid gap-0 md:grid-cols-3 md:gap-8">
                @foreach([
                    ['Cari', 'Temukan pemasok berdasarkan kategori industri, lokasi, dan sertifikasi yang Anda butuhkan.', 'magnifying-glass'],
                    ['Bandingkan', 'Lihat kapasitas, MOQ, lead time, dan sertifikasi dalam satu tabel, tanpa perlu tanya satu per satu.', 'view-columns'],
                    ['Hubungi langsung', 'Buka profil, pelajari dokumennya, lalu hubungi pemasok melalui kontak yang tersedia.', 'chat-bubble-left-right'],
                ] as [$title, $description, $icon])
                    <li class="nb-step relative flex gap-4 pb-8 last:pb-0 md:pb-6" data-reveal>
                        @unless($loop->last)<span class="absolute left-4 top-9 bottom-1 w-px bg-brand-200 md:hidden" aria-hidden="true"></span>@endunless
                        <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 ring-1 ring-brand-200 text-sm font-semibold text-brand-700">{{ $loop->iteration }}</span>
                        <div class="space-y-2">
                            <x-dynamic-component :component="'heroicon-o-'.$icon" class="h-5 w-5 text-brand-700 hidden md:block mb-3" />
                            <h3 class="text-lg font-semibold">{{ $title }}</h3><p class="text-sm leading-[22px] text-text-muted">{{ $description }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="nb-section">
        <div class="nb-container grid items-start gap-8 lg:grid-cols-2 lg:gap-12">
            <div><p class="nb-eyebrow">TRANSPARANSI DATA</p><h2 class="nb-h2 mt-2">Kenali pemasok sebelum bekerja sama.</h2><p class="mt-4 text-base leading-6 text-text-muted">Lihat data operasional, status verifikasi, dan dokumen pada profil yang sama. Informasi yang belum tersedia ditandai dengan jelas.</p></div>
            <div class="lg:row-span-2">
                @if($previewSuppliers->first())<x-supplier-preview :supplier="$previewSuppliers->first()" />@else<div class="nb-card p-6 text-sm text-text-muted">Pratinjau profil akan ditampilkan saat katalog tersedia.</div>@endif
            </div>
            <ul class="space-y-5">
                @foreach([
                    ['Status yang jelas', 'Badge hanya ditampilkan untuk pemasok yang ditandai terverifikasi dalam katalog.'],
                    ['Dokumen di satu tempat', 'Periksa sertifikasi dan dokumen yang tersedia sebelum memulai percakapan.'],
                    ['Format yang seragam', 'Bandingkan informasi yang sama pada setiap profil pemasok.'],
                ] as [$title, $description])
                    <li class="flex items-start gap-3"><x-heroicon-o-check-circle class="mt-0.5 h-5 w-5 shrink-0 text-brand-700" /><div><h3 class="text-[15px] font-semibold">{{ $title }}</h3><p class="mt-1 text-sm leading-[22px] text-text-muted">{{ $description }}</p></div></li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="nb-section bg-white">
        <div class="nb-container">
            <div class="flex flex-wrap items-end justify-between gap-3"><div><p class="nb-eyebrow">KATEGORI INDUSTRI</p><h2 class="nb-h2 mt-2">Jelajahi berdasarkan kategori</h2></div><a href="{{ route('discover.index') }}" class="nb-ghost !px-0 text-sm">Lihat semua sektor →</a></div>
            <div class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
                @foreach($catalogCategories as $category)
                    <a href="{{ route('discover.index', ['category' => $category->name]) }}" class="nb-card-interactive flex flex-col gap-4 p-4 md:p-5" data-reveal>
                        <span class="nb-category-icon flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><x-dynamic-component :component="'heroicon-o-'.\App\Support\SupplierFormat::categoryIcon($category->name)" class="h-5 w-5" /></span>
                        <div><h3 class="text-[15px] font-semibold leading-5">{{ \App\Support\SupplierFormat::category($category->name) }}</h3><p class="mt-2 text-13 text-text-muted">{{ $category->count }} pemasok</p></div>
                    </a>
                @endforeach
                @if($catalogCategories->count() % 2 !== 0)
                    <a href="{{ route('discover.index') }}" class="nb-card flex items-center justify-center gap-2 p-4 text-sm font-semibold text-brand-700">Lihat semua sektor →</a>
                @endif
            </div>
        </div>
    </section>

    <section id="paket" class="nb-section nb-pricing-section">
        <div class="nb-container">
            <p class="nb-eyebrow">PAKET NAMABIZ</p><h2 class="nb-h2 mt-2">Mulai gratis. Upgrade saat Anda butuh data lengkap.</h2>
            <div class="mt-8 grid gap-5 md:grid-cols-2">
                <article class="nb-premium-card flex min-w-0 flex-col p-6 md:p-8" data-reveal>
                    <div class="flex flex-wrap items-center justify-between gap-3"><span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-900"><x-heroicon-s-shield-check class="h-4 w-4" />Direkomendasikan</span><x-heroicon-o-squares-plus class="h-7 w-7 text-emerald-200" /></div>
                    <h3 class="mt-6 text-2xl font-semibold text-white">Premium</h3>
                    <x-premium-price class="mt-3" />
                    <p class="mt-5 text-sm text-slate-300">Semua fitur Gratis, plus:</p>
                    <ul class="mt-5 space-y-4 text-sm mb-8">
                        @foreach(['Metrik operasional', 'Semua sertifikasi', 'Pustaka dokumen lengkap', 'Ekspor PDF perbandingan'] as $feature)<li class="flex gap-2 text-white"><x-heroicon-o-check-circle class="w-5 h-5 shrink-0 text-emerald-200" />{{ $feature }}</li>@endforeach
                    </ul>
                    <div class="mt-auto space-y-4">
                        <a href="{{ route('upgrade') }}" class="nb-button w-full bg-white text-slate-900 hover:bg-emerald-50">Pelajari akses Premium<x-heroicon-o-arrow-right class="h-4 w-4" /></a>
                        <x-premium-purchase id="landing-premium" :show-price="false" :inverse="true" />
                    </div>
                </article>
                <article class="nb-card flex min-w-0 flex-col p-6 md:p-8" data-reveal>
                    <span class="self-start nb-chip">Untuk memulai</span><h3 class="mt-6 text-2xl font-semibold">Gratis</h3>
                    <p class="nb-price mt-3"><span>Rp0</span><span class="nb-price-period">/ mulai sekarang</span></p>
                    <p class="mt-5 text-sm text-text-muted">Langkah awal menemukan mitra bisnis.</p>
                    <ul class="mt-5 space-y-4 text-sm mb-8">
                        @foreach(['Cari pemasok dan lihat profil dasar', 'Hubungi pemasok langsung', 'Bandingkan hingga 4 pemasok (info dasar)'] as $feature)<li class="flex gap-2"><x-heroicon-o-check class="w-5 h-5 shrink-0 text-brand-700" />{{ $feature }}</li>@endforeach
                        <li class="flex flex-wrap items-center gap-2 text-text-muted"><x-heroicon-o-lock-closed class="w-5 h-5" />Metrik, dokumen lengkap & PDF <span class="nb-verified">Premium</span></li>
                    </ul>
                    <a href="{{ route('register') }}" class="nb-secondary mt-auto w-full">Mulai gratis</a>
                </article>
            </div>
        </div>
    </section>

    <section class="nb-section bg-white" id="faq">
        <div class="nb-container">
            <p class="nb-eyebrow">TANYA JAWAB</p><h2 class="nb-h2 mt-2">Pertanyaan yang sering diajukan</h2>
            <div class="mt-8 max-w-[720px] space-y-3" x-data="{ active: null }">
                @foreach([
                    ['Apakah Namabiz gratis digunakan?', 'Ya. Anda dapat menelusuri katalog, melihat informasi dasar, dan menghubungi pemasok melalui kontak yang tersedia.'],
                    ['Apa arti badge Terverifikasi?', config('namabiz.draft.label').' '.config('namabiz.draft.verification')],
                    ['Berapa harga Premium?', \App\Support\PortalContent::premiumPriceLabel().'. '.config('namabiz.billing.availability')],
                    ['Bagaimana cara membatalkan langganan?', config('namabiz.draft.label').' '.config('namabiz.draft.cancellation')],
                    ['Metode pembayaran apa yang tersedia?', config('namabiz.draft.label').' '.config('namabiz.draft.payments')],
                    ['Bagaimana cara menghubungi pemasok?', 'Buka profil pemasok, pelajari informasi yang tersedia, lalu gunakan tombol WhatsApp atau email. Kontak hanya ditampilkan jika telah diisi oleh pemasok.'],
                ] as [$question, $answer])
                    <div class="rounded-xl border border-border">
                        <h3><button type="button" @click="active = active === {{ $loop->index }} ? null : {{ $loop->index }}" :aria-expanded="active === {{ $loop->index }}" aria-controls="faq-answer-{{ $loop->index }}" id="faq-question-{{ $loop->index }}" class="flex min-h-[56px] w-full items-center justify-between gap-4 p-4 text-left text-base font-semibold">
                            {{ $question }}<x-heroicon-o-chevron-down class="h-5 w-5 shrink-0 text-text-muted transition-transform" ::class="active === {{ $loop->index }} ? 'rotate-180' : ''" />
                        </button></h3>
                        <div id="faq-answer-{{ $loop->index }}" aria-labelledby="faq-question-{{ $loop->index }}" x-show="active === {{ $loop->index }}" x-collapse x-cloak><p class="px-4 pb-5 text-sm leading-[22px] text-text-muted">{{ $answer }}</p></div>
                    </div>
                @endforeach
                <a href="{{ \App\Support\PortalContent::pageUrl('help') }}" class="nb-ghost !px-0 text-brand-700">Masih bingung? Buka panduan bantuan →</a>
                <p class="text-sm text-text-muted">Informasi langganan mengacu pada <a class="inline-flex min-h-[44px] items-center text-brand-700 underline underline-offset-4" href="{{ config('namabiz.links.play_cancellation') }}" target="_blank" rel="noopener noreferrer">panduan resmi Google Play</a>.</p>
            </div>
        </div>
    </section>

    <section class="nb-section">
        <div class="nb-container">
            <div class="nb-cta grid gap-6 rounded-[20px] bg-slate-900 p-6 md:p-10 lg:grid-cols-[1fr_auto] lg:items-center">
                <div><h2 class="text-2xl leading-[30px] font-bold text-white">Langkah pertama menuju<br class="hidden md:block"> mitra bisnis berikutnya.</h2><p class="mt-3 text-sm leading-[22px] text-slate-300">Jelajahi katalog dan susun pilihan pemasok sesuai kebutuhan Anda.</p></div>
                <div class="grid gap-3 lg:min-w-[240px]"><a href="{{ route('register') }}" class="nb-accent">Buat akun gratis</a><a href="{{ route('discover.index') }}" class="nb-button border border-white/40 text-white hover:bg-white/10">Jelajahi pemasok</a></div>
            </div>
        </div>
    </section>
</x-app-layout>
