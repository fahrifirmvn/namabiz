<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] #[Title('Lupa Kata Sandi')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="space-y-6">
    <div>
        <h2 class="text-xl font-extrabold text-gray-900">Lupa Kata Sandi?</h2>
        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
            Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-4">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" placeholder="nama@perusahaan.co.id" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full" wire:loading.attr="disabled" wire:target="sendPasswordResetLink">
                <span wire:loading.remove wire:target="sendPasswordResetLink">Kirim Tautan Atur Ulang</span>
                <span wire:loading wire:target="sendPasswordResetLink">Mengirim...</span>
            </x-primary-button>
        </div>
    </form>

    <div class="pt-4 border-t border-gray-100 text-center text-xs text-gray-600">
        Ingat kata sandi Anda?
        <a href="{{ route('login') }}" wire:navigate class="font-bold text-black hover:underline ms-1">
            Kembali masuk
        </a>
    </div>
</div>
