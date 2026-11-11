<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] #[Title('Verifikasi Email')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="space-y-6">
    <div>
        <h2 class="text-xl font-extrabold text-gray-900">Verifikasi Alamat Email Anda</h2>
        <p class="text-xs text-gray-600 mt-1 leading-relaxed">
            Terima kasih telah mendaftar! Sebelum melanjutkan, silakan verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan. Jika belum menerima email, kami dapat mengirimkannya kembali.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 font-medium text-xs text-emerald-800">
            Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
        </div>
    @endif

    <div class="pt-2 flex items-center justify-between gap-4">
        <x-primary-button wire:click="sendVerification" wire:loading.attr="disabled" wire:target="sendVerification">
            <span wire:loading.remove wire:target="sendVerification">Kirim Ulang Email Verifikasi</span>
            <span wire:loading wire:target="sendVerification">Mengirim...</span>
        </x-primary-button>

        <button wire:click="logout" type="submit" class="underline text-xs font-semibold text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
            Keluar
        </button>
    </div>
</div>
