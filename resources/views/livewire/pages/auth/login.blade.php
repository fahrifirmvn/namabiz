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

<div class="space-y-6"
     x-data="{
         show: false,
         emailVal: @entangle('form.email'),
         passwordVal: @entangle('form.password'),
         touched: { email: false, password: false },
         get isEmailValid() { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.emailVal || ''); }
     }">
    <div>
        <h2 class="text-xl sm:text-2xl font-extrabold text-text-primary tracking-tight">Selamat datang kembali</h2>
        <p class="text-13 text-text-secondary mt-1">
            Masuk untuk mengakses daftar pemasok tersimpan, koleksi, dan dokumen katalog.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4" novalidate>
        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat email" />
            <x-text-input wire:model="form.email"
                          id="email"
                          class="block mt-1.5 w-full"
                          type="email"
                          name="email"
                          placeholder="nama@perusahaan.co.id"
                          @blur="touched.email = true"
                          required
                          autofocus
                          autocomplete="username" />
            <p x-show="touched.email && !isEmailValid" x-cloak class="mt-1.5 text-xs text-danger">
                Masukkan alamat email yang valid.
            </p>
            <x-input-error :messages="$errors->get('form.email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" value="Kata sandi" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-text-secondary hover:text-text-primary hover:underline rounded nb-focus-ring" href="{{ route('password.request') }}" wire:navigate>
                        Lupa kata sandi?
                    </a>
                @endif
            </div>

            <div class="relative mt-1.5">
                <x-text-input wire:model="form.password"
                              id="password"
                              class="block w-full pr-11"
                              x-bind:type="show ? 'text' : 'password'"
                              name="password"
                              placeholder="Masukkan kata sandi"
                              @blur="touched.password = true"
                              required
                              autocomplete="current-password" />
                <button type="button"
                        @click="show = !show"
                        class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-text-muted hover:text-text-primary rounded-r-xl nb-focus-ring"
                        :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                    <x-heroicon-o-eye x-show="!show" class="w-4 h-4" />
                    <x-heroicon-o-eye-slash x-show="show" x-cloak class="w-4 h-4" />
                </button>
            </div>

            <p x-show="touched.password && !(passwordVal || '').length" x-cloak class="mt-1.5 text-xs text-danger">
                Kata sandi wajib diisi.
            </p>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1.5" />
        </div>

        <!-- Remember Me -->
        <div class="pt-1">
            <label for="remember" class="inline-flex items-center cursor-pointer">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-gray-900 shadow-2xs focus:ring-brand-600" name="remember">
                <span class="ms-2 text-13 text-text-secondary">Ingat saya di perangkat ini</span>
            </label>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full" wire:loading.attr="disabled" wire:target="login">
                <span wire:loading.remove wire:target="login">Masuk</span>
                <span wire:loading.inline-flex wire:target="login" class="items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Memproses...</span>
                </span>
            </x-primary-button>
        </div>
    </form>

    <div class="pt-4 border-t border-border-subtle text-center text-13 text-text-secondary">
        Belum punya akun?
        <a href="{{ route('register') }}" wire:navigate class="font-semibold text-text-primary hover:underline ms-1 nb-focus-ring rounded">
            Daftar sekarang
        </a>
    </div>
</div>
