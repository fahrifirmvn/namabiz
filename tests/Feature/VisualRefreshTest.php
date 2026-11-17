<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Support\PortalContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisualRefreshTest extends TestCase
{
    use RefreshDatabase;

    public function test_price_uses_the_same_configured_amount_on_landing_faq_and_upgrade(): void
    {
        config()->set('namabiz.billing.price.amount', 175000);

        $this->get('/')->assertSee('Rp175.000')->assertSee('Rp175.000/bulan')->assertDontSee('Rp150.000');
        $this->assertSame('Rp175.000/bulan', PortalContent::premiumPriceLabel());
    }

    public function test_public_catalog_pages_never_display_demo_labels(): void
    {
        $supplier = Supplier::factory()->create([
            'name' => 'PT Kemasan Nusantara',
            'slug' => 'pt-kemasan-nusantara',
        ]);

        foreach (['/', '/discover', route('suppliers.show', $supplier->slug), '/login'] as $path) {
            $this->get($path)
                ->assertSee('PT Kemasan Nusantara')
                ->assertDontSee('Data demonstrasi')
                ->assertDontSee('(Contoh)');
        }
    }
}
