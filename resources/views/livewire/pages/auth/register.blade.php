<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] #[Title('Daftar Akun')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public int $step = 1;

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.lowercase' => 'Gunakan huruf kecil untuk alamat email.',
            'email.max' => 'Alamat email maksimal 255 karakter.',
            'email.unique' => 'Alamat email sudah terdaftar. Silakan masuk.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal :min karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi belum cocok.',
        ];
    }

    public function nextStep(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $this->step = 2;
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $throttleKey = 'web-register|'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan pendaftaran. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        RateLimiter::hit($throttleKey, 60);

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
         get isEmailValid() { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.emailVal || ''); }
     }">
    <div>
        <p class="nb-eyebrow mb-3" role="status">Langkah {{ $step }}/2 · {{ $step === 1 ? 'Akun Anda' : 'Kenali Anda' }}</p>
        <div class="grid grid-cols-2 gap-2 mb-6" aria-hidden="true"><span class="h-1 rounded-full bg-brand-700"></span><span class="h-1 rounded-full {{ $step === 2 ? 'bg-brand-700' : 'bg-slate-200' }}"></span></div>
        <h1 class="text-[26px] leading-8 font-bold text-text-primary tracking-tight">Buat akun gratis</h1>
        <p class="text-sm text-text-muted mt-3">
            Simpan pilihan pemasok dan kelola kebutuhan bisnis Anda di satu tempat.
        </p>
    </div>

    <form wire:submit="{{ $step === 1 ? 'nextStep' : 'register' }}" class="space-y-4" novalidate>
        @if($step === 2)
        <div class="flex items-center justify-between gap-3 rounded-xl bg-surface p-4"><p class="text-sm break-all">{{ $email }}</p><button type="button" wire:click="$set('step', 1)" class="nb-ghost !px-2 shrink-0">Ubah</button></div>
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
            <p x-show="touched.name && (nameVal || '').length > 0 && !(nameVal || '').trim()" x-cloak class="mt-1.5 text-xs text-danger">
                Nama lengkap wajib diisi.
            </p>
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>
        @endif

        <!-- Email Address -->
        @if($step === 1)
        <div>
            <x-input-label for="email" value="Email kerja atau bisnis" />
            <x-text-input wire:model="email"
                          id="email"
                          class="block mt-1.5 w-full"
                           type="email"
                           inputmode="email"
                           aria-describedby="register-email-error"
                           aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                          name="email"
                          placeholder="budi@perusahaan.co.id"
                          @blur="touched.email = true"
                          required
                          autocomplete="email" />
            <p x-show="touched.email && (emailVal || '').trim().length > 0 && !isEmailValid" x-cloak class="mt-1.5 text-xs text-danger">
                Masukkan alamat email yang valid (contoh: nama@perusahaan.co.id).
            </p>
            <x-input-error :messages="$errors->get('email')" id="register-email-error" class="mt-1.5" />
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
                               aria-describedby="register-password-error"
                               aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
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
                <li class="text-text-muted">Disarankan: kombinasi huruf dan angka.</li>
            </ul>

            <p x-show="touched.password && (passwordVal || '').length > 0 && !hasMinLength" x-cloak class="mt-1.5 text-xs text-danger">
                Kata sandi harus terdiri dari minimal 8 karakter.
            </p>
            <x-input-error :messages="$errors->get('password')" id="register-password-error" class="mt-1.5" />
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
        @endif

        <div class="pt-2">
            <x-primary-button class="w-full" wire:loading.attr="disabled" wire:target="register,nextStep">
                <span wire:loading.remove wire:target="register,nextStep">{{ $step === 1 ? 'Lanjutkan' : 'Buat akun gratis' }}</span>
                <span wire:loading.inline-flex wire:target="register,nextStep" class="items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Memproses...</span>
                </span>
            </x-primary-button>
        </div>
    </form>

    <div class="pt-4 border-t border-border-subtle text-center text-sm text-text-secondary">
        Sudah punya akun?
        <a href="{{ route('login') }}" wire:navigate class="inline-flex min-h-[44px] items-center font-semibold text-brand-700 hover:underline ms-1 nb-focus-ring rounded">
            Masuk di sini
        </a>
    </div>
</div>
