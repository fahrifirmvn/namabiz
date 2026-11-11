<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] #[Title('Masuk Akun')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="space-y-6">
    <div>
        <h2 class="text-xl font-extrabold text-gray-900">Masuk ke Akun Anda</h2>
        <p class="text-xs text-gray-500 mt-1">
            Kelola daftar pemasok tersimpan, koleksi pengadaan, dan akses dokumen katalog.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" placeholder="nama@perusahaan.co.id" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <div class="flex items-center justify-between">
                <x-input-label for="password" value="Kata Sandi" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-gray-600 hover:text-black hover:underline rounded focus:outline-none focus:ring-2 focus:ring-black" href="{{ route('password.request') }}" wire:navigate>
                        Lupa kata sandi?
                    </a>
                @endif
            </div>

            <div class="relative mt-1">
                <x-text-input wire:model="form.password" id="password" class="block w-full pr-10"
                                x-bind:type="show ? 'text' : 'password'"
                                name="password"
                                placeholder="Masukkan kata sandi"
                                required autocomplete="current-password" />
                <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-700"
                        :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                    <x-heroicon-o-eye x-show="!show" class="w-4 h-4" />
                    <x-heroicon-o-eye-slash x-show="show" x-cloak class="w-4 h-4" />
                </button>
            </div>

            <x-input-error :messages="$errors->get('form.password')" class="mt-1.5" />
        </div>

        <!-- Remember Me -->
        <div class="pt-1">
            <label for="remember" class="inline-flex items-center cursor-pointer">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-black shadow-xs focus:ring-black" name="remember">
                <span class="ms-2 text-xs text-gray-600">Ingat saya di perangkat ini</span>
            </label>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full" wire:loading.attr="disabled" wire:target="login">
                <span wire:loading.remove wire:target="login">Masuk</span>
                <span wire:loading wire:target="login">Memproses...</span>
            </x-primary-button>
        </div>
    </form>

    <div class="pt-4 border-t border-gray-100 text-center text-xs text-gray-600">
        Belum memiliki akun?
        <a href="{{ route('register') }}" wire:navigate class="font-bold text-black hover:underline ms-1">
            Daftar sekarang
        </a>
    </div>
</div>
