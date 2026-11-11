<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register')
            ->assertSee('Buat akun gratis')
            ->assertSee('Temukan pabrik yang pas untuk bisnismu, lebih cepat.')
            ->assertSee('12.400+ pemasok')
            ->assertSee('Minimal 8 karakter')
            ->assertSee('Kombinasi huruf dan angka')
            ->assertSee('Masuk di sini');
    }

    public function test_new_users_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_registration_rejects_short_or_unconfirmed_passwords(): void
    {
        Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'short')
            ->set('password_confirmation', 'mismatch')
            ->call('register')
            ->assertHasErrors(['password'])
            ->assertNoRedirect();

        $this->assertGuest();
    }
}
