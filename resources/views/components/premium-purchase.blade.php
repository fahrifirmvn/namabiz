@props(['showPrice' => true, 'inverse' => false])
<div {{ $attributes->merge(['class' => 'relative space-y-3']) }}>
    @if($showPrice)<x-premium-price />@endif
    <p class="text-sm leading-[22px] {{ $inverse ? 'text-slate-300' : 'text-text-muted' }}">{{ config('namabiz.billing.availability') }}</p>
    <button type="button" disabled class="nb-purchase-disabled w-full {{ $inverse ? 'nb-purchase-disabled-inverse' : '' }}" aria-describedby="purchase-unavailable-{{ $attributes->get('id', 'premium') }}">Beli Premium <x-heroicon-o-lock-closed class="h-4 w-4" /></button>
    <span id="purchase-unavailable-{{ $attributes->get('id', 'premium') }}" class="sr-only">Pembelian dinonaktifkan: checkout produksi belum tersedia.</span>
</div>
