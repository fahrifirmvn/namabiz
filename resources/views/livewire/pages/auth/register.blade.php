<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] #[Title('Daftar Akun')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="space-y-6" x-data="{ showPassword: false }">
    <div>
        <h2 class="text-xl font-extrabold text-gray-900">Buat Akun Pembeli Baru</h2>
        <p class="text-xs text-gray-500 mt-1">
            Daftar gratis untuk menyimpan pemasok, mengunduh katalog, dan memberikan ulasan.
        </p>
    </div>

    <form wire:submit="register" class="space-y-4">
        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" placeholder="Contoh: Budi Santoso" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" placeholder="budi@perusahaan.co.id" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Sandi" />

            <div class="relative mt-1">
                <x-text-input wire:model="password" id="password" class="block w-full pr-10"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                name="password"
                                placeholder="Minimal 8 karakter"
                                required autocomplete="new-password" />
                <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-700"
                        :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                    <x-heroicon-o-eye x-show="!showPassword" class="w-4 h-4" />
                    <x-heroicon-o-eye-slash x-show="showPassword" x-cloak class="w-4 h-4" />
                </button>
            </div>

            <p class="text-2xs text-gray-400 mt-1">Gunakan minimal 8 karakter dengan kombinasi huruf dan angka.</p>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            x-bind:type="showPassword ? 'text' : 'password'"
                            placeholder="Ulangi kata sandi"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full" wire:loading.attr="disabled" wire:target="register">
                <span wire:loading.remove wire:target="register">Buat Akun</span>
                <span wire:loading wire:target="register">Mendaftarkan...</span>
            </x-primary-button>
        </div>
    </form>

    <div class="pt-4 border-t border-gray-100 text-center text-xs text-gray-600">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" wire:navigate class="font-bold text-black hover:underline ms-1">
            Masuk di sini
        </a>
    </div>
</div>
