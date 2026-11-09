<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        @if (session('status'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 mb-8 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        @if (session('paywall_triggered'))
            <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 mb-8 text-xs font-semibold flex items-center justify-center gap-2">
                <x-heroicon-s-lock-closed class="w-4 h-4 text-amber-600" />
                Fitur atau dokumen tersebut memerlukan langganan paket Premium aktif.
            </div>
        @endif

        <div class="w-16 h-16 bg-black text-white rounded-2xl mx-auto flex items-center justify-center mb-6 shadow-sm">
            <x-heroicon-s-sparkles class="w-8 h-8 text-amber-300" />
        </div>

        <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">PAKET LANGGANAN NAMABIZ</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mt-1">
            Upgrade ke Premium
        </h1>
        <p class="text-sm text-gray-600 mt-2 max-w-xl mx-auto">
            Buka visibilitas parameter analitis mendalam, validasi audit pabrik resmi, dan ekspor perbandingan tanpa batasan.
        </p>

        <div class="bg-white border-2 border-black rounded-2xl p-8 mt-8 text-left shadow-lg">
            <div class="flex items-center justify-between pb-6 border-b border-gray-100">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Paket Enterprise / Pengadaan</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Akses penuh untuk tim sourcing dan manajemen rantai pasok.</p>
                </div>
                <span class="px-3 py-1 bg-black text-white text-2xs uppercase tracking-wider font-bold rounded-full">
                    Rekomendasi
                </span>
            </div>

            <ul class="space-y-3.5 text-xs sm:text-sm text-gray-800 mt-6">
                <li class="flex items-center gap-3">
                    <x-heroicon-s-check-circle class="w-4 h-4 text-emerald-600 shrink-0" />
                    <span>Seluruh metrik operasional terpasang (Kapasitas riil, MOQ terendah, Lead time pengiriman)</span>
                </li>
                <li class="flex items-center gap-3">
                    <x-heroicon-s-check-circle class="w-4 h-4 text-emerald-600 shrink-0" />
                    <span>Laporan kepatuhan lengkap &amp; sertifikasi resmi (ISO 9001, ISO 14001, HACCP, Halal)</span>
                </li>
                <li class="flex items-center gap-3">
                    <x-heroicon-s-check-circle class="w-4 h-4 text-emerald-600 shrink-0" />
                    <span>Pustaka dokumen tervalidasi &amp; laporan audit pihak ketiga tanpa batas</span>
                </li>
                <li class="flex items-center gap-3">
                    <x-heroicon-s-check-circle class="w-4 h-4 text-emerald-600 shrink-0" />
                    <span>Ekspor lembar komparasi pemasok ke berkas PDF resmi</span>
                </li>
            </ul>

            @if(auth()->user()?->isPremium())
                <div class="mt-8 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
                    <p class="text-xs font-bold text-emerald-800">Akun Anda saat ini memiliki akses Premium aktif.</p>
                    <p class="text-2xs text-emerald-600 mt-0.5">Berlaku hingga {{ auth()->user()->premium_expires_at?->format('d M Y') ?? '1 tahun ke depan' }}.</p>
                </div>
            @else
                <form method="POST" action="{{ route('upgrade.checkout') }}" class="mt-8">
                    @csrf
                    <button type="submit" class="w-full bg-black text-white hover:bg-gray-800 rounded-xl py-3.5 font-bold text-sm transition shadow-sm">
                        Aktivasi Akses Premium Sekarang &rarr;
                    </button>
                    <p class="text-2xs text-gray-400 text-center mt-2.5">Simulasi checkout langsung aktif tanpa kartu kredit untuk keperluan evaluasi.</p>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
