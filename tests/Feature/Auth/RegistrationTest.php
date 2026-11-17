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
            ->assertSee('Temukan pemasok yang pas untuk bisnis Anda.')
            ->assertDontSee('12.400+ pemasok')
            ->assertSee('Langkah 1/2')
            ->assertSee('Minimal 8 karakter')
            ->assertSee('Disarankan: kombinasi huruf dan angka.')
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

    public function test_registration_validates_credentials_before_showing_the_second_step(): void
    {
        Volt::test('pages.auth.register')
            ->set('email', 'invalid-email')
            ->set('password', 'short')
            ->call('nextStep')
            ->assertHasErrors(['email', 'password'])
            ->assertSee('Masukkan alamat email yang valid.')
            ->assertSet('step', 1);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_wizard_preserves_credentials_and_registers_after_the_name_is_entered(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('email', 'buyer@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('nextStep')
            ->assertHasNoErrors()
            ->assertSet('step', 2)
            ->assertSee('Nama lengkap');

        $this->assertDatabaseCount('users', 0);

        $component->set('name', 'Buyer Test')
            ->call('register')
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', ['name' => 'Buyer Test', 'email' => 'buyer@example.com']);
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
