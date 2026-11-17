<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use App\Support\PortalContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class PortalContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('namabiz.contact', [
            'email' => null, 'address' => null, 'whatsapp' => null, 'whatsapp_portal_source' => null,
            'whatsapp_message' => 'Halo tim Namabiz', 'social' => [],
        ]);
    }

    #[TestWith(['/privasi', 'Privasi'])]
    #[TestWith(['/syarat-ketentuan', 'Syarat & Ketentuan'])]
    #[TestWith(['/bantuan', 'Bantuan'])]
    #[TestWith(['/tentang', 'Tentang Namabiz'])]
    #[TestWith(['/kontak', 'Kontak'])]
    public function test_information_pages_are_accessible_and_explicitly_drafts(string $path, string $title): void
    {
        $this->get($path)->assertOk()->assertSee($title)
            ->assertSee('[DRAF - PERLU REVIEW]')
            ->assertDontSee('[ISI:', false);
    }

    public function test_footer_points_to_real_information_pages_and_omits_empty_contacts(): void
    {
        $response = $this->get('/');

        foreach (['/privasi', '/syarat-ketentuan', '/bantuan', '/tentang', '/kontak'] as $path) {
            $response->assertSee('href="'.url($path).'"', false);
        }
        $response->assertSee('Gratis untuk memulai. Tanpa Biaya')
            ->assertDontSee('[ISI:', false)
            ->assertDontSee('mailto:', false)
            ->assertDontSee('https://wa.me/', false)
            ->assertDontSee('Instagram')
            ->assertDontSee('WhatsApp:');
    }

    public function test_contact_values_are_rendered_only_from_central_configuration_and_escaped(): void
    {
        config()->set('namabiz.contact.email', 'owner@namabiz.test');
        config()->set('namabiz.contact.address', '<script>alert("test")</script>');
        config()->set('namabiz.contact.social', ['Instagram' => 'https://social.example.test/namabiz']);

        $this->get('/kontak')->assertSee('mailto:owner@namabiz.test', false)
            ->assertSee('https://social.example.test/namabiz', false)
            ->assertSee('<script>alert("test")</script>')
            ->assertDontSee('<script>alert("test")</script>', false);
    }

    #[TestWith(['javascript:alert(1)'])]
    #[TestWith(['http://social.example.test/namabiz'])]
    #[TestWith(['https://user:password@social.example.test'])]
    public function test_unsafe_configured_social_links_are_not_rendered(string $url): void
    {
        config()->set('namabiz.contact.social', ['Instagram' => $url]);

        $this->get('/kontak')->assertDontSee('Instagram');
    }

    #[TestWith([null])]
    #[TestWith(['https://other.example.test/kontak'])]
    #[TestWith(['https://namabiz.com.other.example.test/kontak'])]
    public function test_curator_whatsapp_requires_an_official_portal_source(?string $source): void
    {
        config()->set('namabiz.contact.whatsapp', '081111111111');
        config()->set('namabiz.contact.whatsapp_portal_source', $source);

        $this->get('/discover')->assertDontSee('https://wa.me/', false)
            ->assertSee('Buka panduan bantuan');
    }

    public function test_curator_whatsapp_is_normalized_and_uses_the_configured_message(): void
    {
        config()->set('namabiz.contact.whatsapp', '0811-1111-1111');
        config()->set('namabiz.contact.whatsapp_portal_source', 'https://namabiz.com/kontak');

        $this->assertSame('https://wa.me/6281111111111?text=Halo%20tim%20Namabiz', PortalContent::whatsappUrl());
        $this->get('/kontak')->assertSee('https://wa.me/6281111111111?text=Halo%20tim%20Namabiz', false);
    }

    #[TestWith(['not-a-phone'])]
    #[TestWith(['123'])]
    public function test_invalid_curator_phone_is_hidden_even_with_an_official_source(string $number): void
    {
        config()->set('namabiz.contact.whatsapp', $number);
        config()->set('namabiz.contact.whatsapp_portal_source', 'https://namabiz.com/kontak');

        $this->get('/kontak')->assertDontSee('https://wa.me/', false);
    }

    public function test_owner_monthly_price_is_displayed_and_the_web_buy_button_stays_disabled(): void
    {
        $this->get('/')->assertSee('Rp150.000')
            ->assertSee('/ bulan')
            ->assertSee('Rp150.000/bulan')
            ->assertSee('type="button" disabled', false)
            ->assertDontSee('Rp [ISI:', false);

        $this->actingAs(User::factory()->create())->get(route('upgrade'))
            ->assertSee('Rp150.000')
            ->assertSee('/ bulan')
            ->assertSee('type="button" disabled', false)
            ->assertDontSee('action="'.route('upgrade.checkout').'"', false);
    }

    public function test_manually_posting_to_the_old_checkout_cannot_activate_premium(): void
    {
        $user = User::factory()->create(['subscription_tier' => 'free', 'premium_expires_at' => null]);

        $this->actingAs($user)->post(route('upgrade.checkout'))->assertForbidden();

        $this->assertSame('free', $user->fresh()->subscription_tier);
        $this->assertNull($user->fresh()->premium_expires_at);
    }

    public function test_unsupported_auth_options_are_hidden_even_if_a_flag_is_enabled_without_a_web_route(): void
    {
        config()->set('namabiz.features.google_login', true);
        config()->set('namabiz.features.whatsapp_otp', true);

        $this->get('/login')->assertDontSee('Lanjut dengan Google')
            ->assertDontSee('Masuk dengan OTP WhatsApp')
            ->assertDontSee('[ISI:', false);
    }

    public function test_auth_options_require_both_the_feature_flag_and_a_real_web_route(): void
    {
        Route::get('/test/google-redirect', fn () => response('test-only handler'))->middleware('web')->name('test.google-redirect');
        Route::getRoutes()->refreshNameLookups();
        config()->set('namabiz.auth_options.google_login.route', 'test.google-redirect');
        config()->set('namabiz.features.google_login', false);
        $this->get('/login')->assertDontSee('Lanjut dengan Google');

        config()->set('namabiz.features.google_login', true);

        $this->get('/login')->assertSee('Lanjut dengan Google')
            ->assertSee('href="'.route('test.google-redirect').'"', false);
    }

    public function test_mobile_post_auth_endpoint_cannot_be_rendered_as_a_web_login_link(): void
    {
        config()->set('namabiz.features.google_login', true);
        config()->set('namabiz.auth_options.google_login.route', 'api.auth.google');

        $this->get('/login')->assertDontSee('Lanjut dengan Google');
    }

    public function test_registration_does_not_offer_fields_the_backend_cannot_store(): void
    {
        Volt::test('pages.auth.register')
            ->set('email', 'buyer@namabiz.test')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('nextStep')
            ->assertSee('Nama lengkap')
            ->assertDontSee('Nama perusahaan')
            ->assertDontSee('Buyer/Pemasok')
            ->assertDontSee('nomor WhatsApp')
            ->assertDontSee('[ISI:');
    }

    public function test_drafts_describe_recorded_statuses_and_link_official_google_play_guidance(): void
    {
        $this->get('/bantuan')->assertSee('penanda profil yang dapat diubah admin')
            ->assertSee('bukan bukti audit fisik pabrik')
            ->assertSee('2,5 juta unit/bln')
            ->assertSee('4–6 minggu')
            ->assertSee('https://support.google.com/googleplay/answer/7018481?hl=id', false)
            ->assertSee('https://support.google.com/googleplay/answer/2651410?hl=id', false);
    }

    public function test_missing_supplier_email_does_not_create_an_address_from_its_website(): void
    {
        $supplier = Supplier::factory()->create(['email' => null, 'website' => 'https://supplier.example.test', 'whatsapp' => null]);

        $this->get(route('suppliers.show', $supplier->slug))
            ->assertDontSee('sales@supplier.example.test')
            ->assertDontSee('mailto:', false)
            ->assertDontSee('contact@namabiz.com');
    }
}
