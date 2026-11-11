<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] #[Title('Konfirmasi Kata Sandi')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="space-y-6" x-data="{ show: false }">
    <div>
        <h2 class="text-xl font-extrabold text-gray-900">Konfirmasi Kata Sandi</h2>
        <p class="text-xs text-gray-600 mt-1 leading-relaxed">
            Ini adalah area aman aplikasi. Harap konfirmasi kata sandi Anda sebelum melanjutkan.
        </p>
    </div>

    <form wire:submit="confirmPassword" class="space-y-4">
        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Sandi" />

            <div class="relative mt-1">
                <x-text-input wire:model="password"
                              id="password"
                              class="block w-full pr-10"
                              x-bind:type="show ? 'text' : 'password'"
                              name="password"
                              required autocomplete="current-password" />
                <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-700"
                        :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                    <x-heroicon-o-eye x-show="!show" class="w-4 h-4" />
                    <x-heroicon-o-eye-slash x-show="show" x-cloak class="w-4 h-4" />
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full" wire:loading.attr="disabled" wire:target="confirmPassword">
                <span wire:loading.remove wire:target="confirmPassword">Konfirmasi</span>
                <span wire:loading wire:target="confirmPassword">Memverifikasi...</span>
            </x-primary-button>
        </div>
    </form>
</div>
