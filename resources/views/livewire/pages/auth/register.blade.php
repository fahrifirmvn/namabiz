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

<div class="space-y-6"
     x-data="{
         showPassword: false,
         nameVal: @entangle('name'),
         emailVal: @entangle('email'),
         passwordVal: @entangle('password'),
         confirmVal: @entangle('password_confirmation'),
         touched: { name: false, email: false, password: false, confirm: false },
         get hasMinLength() { return (this.passwordVal || '').length >= 8; },
         get hasLettersAndNumbers() { return /[A-Za-z]/.test(this.passwordVal || '') && /\d/.test(this.passwordVal || ''); },
         get isEmailValid() { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.emailVal || ''); }
     }">
    <div>
        <h2 class="text-xl sm:text-2xl font-extrabold text-text-primary tracking-tight">Buat akun gratis</h2>
        <p class="text-13 text-text-secondary mt-1">
            Simpan pemasok, unduh katalog, dan hubungi pabrik dalam hitungan menit.
        </p>
    </div>

    <form wire:submit="register" class="space-y-4" novalidate>
        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama lengkap" />
            <x-text-input wire:model="name"
                          id="name"
                          class="block mt-1.5 w-full"
                          type="text"
                          name="name"
                          placeholder="Contoh: Budi Santoso"
                          @blur="touched.name = true"
                          required
                          autofocus
                          autocomplete="name" />
            <p x-show="touched.name && !(nameVal || '').trim()" x-cloak class="mt-1.5 text-xs text-danger">
                Nama lengkap wajib diisi.
            </p>
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email kerja atau bisnis" />
            <x-text-input wire:model="email"
                          id="email"
                          class="block mt-1.5 w-full"
                          type="email"
                          name="email"
                          placeholder="budi@perusahaan.co.id"
                          @blur="touched.email = true"
                          required
                          autocomplete="email" />
            <p x-show="touched.email && !isEmailValid" x-cloak class="mt-1.5 text-xs text-danger">
                Masukkan alamat email yang valid (contoh: nama@perusahaan.co.id).
            </p>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata sandi" />

            <div class="relative mt-1.5">
                <x-text-input wire:model="password"
                              id="password"
                              class="block w-full pr-11"
                              x-bind:type="showPassword ? 'text' : 'password'"
                              name="password"
                              placeholder="Minimal 8 karakter"
                              @blur="touched.password = true"
                              required
                              autocomplete="new-password" />
                <button type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-text-muted hover:text-text-primary rounded-r-xl nb-focus-ring"
                        :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                    <x-heroicon-o-eye x-show="!showPassword" class="w-4 h-4" />
                    <x-heroicon-o-eye-slash x-show="showPassword" x-cloak class="w-4 h-4" />
                </button>
            </div>

            <!-- Password Strength Checklist -->
            <ul class="mt-2 space-y-1 text-xs" aria-label="Syarat kekuatan kata sandi">
                <li class="flex items-center gap-1.5 transition-colors"
                    :class="hasMinLength ? 'text-brand-700 font-medium' : 'text-text-secondary'">
                    <x-heroicon-s-check-circle x-show="hasMinLength" x-cloak class="w-4 h-4 text-brand-600 shrink-0" />
                    <span x-show="!hasMinLength" class="w-4 h-4 inline-flex items-center justify-center shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                    </span>
                    <span>Minimal 8 karakter</span>
                </li>
                <li class="flex items-center gap-1.5 transition-colors"
                    :class="hasLettersAndNumbers ? 'text-brand-700 font-medium' : 'text-text-secondary'">
                    <x-heroicon-s-check-circle x-show="hasLettersAndNumbers" x-cloak class="w-4 h-4 text-brand-600 shrink-0" />
                    <span x-show="!hasLettersAndNumbers" class="w-4 h-4 inline-flex items-center justify-center shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                    </span>
                    <span>Kombinasi huruf dan angka</span>
                </li>
            </ul>

            <p x-show="touched.password && !hasMinLength" x-cloak class="mt-1.5 text-xs text-danger">
                Kata sandi harus terdiri dari minimal 8 karakter.
            </p>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi kata sandi" />

            <x-text-input wire:model="password_confirmation"
                          id="password_confirmation"
                          class="block mt-1.5 w-full"
                          x-bind:type="showPassword ? 'text' : 'password'"
                          placeholder="Ketik ulang kata sandi"
                          name="password_confirmation"
                          @blur="touched.confirm = true"
                          required
                          autocomplete="new-password" />

            <p x-show="touched.confirm && confirmVal && confirmVal !== passwordVal" x-cloak class="mt-1.5 text-xs text-danger">
                Konfirmasi kata sandi belum cocok.
            </p>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full" wire:loading.attr="disabled" wire:target="register">
                <span wire:loading.remove wire:target="register">Buat akun gratis</span>
                <span wire:loading.inline-flex wire:target="register" class="items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Membuat akun...</span>
                </span>
            </x-primary-button>
        </div>
    </form>

    <div class="pt-4 border-t border-border-subtle text-center text-13 text-text-secondary">
        Sudah punya akun?
        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-text-primary hover:underline ms-1 nb-focus-ring rounded">
            Masuk di sini
        </a>
    </div>
</div>
