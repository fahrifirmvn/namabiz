<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Temukan pemasok yang tepat, lebih cepat.')
            ->assertSee('INFRASTRUKTUR PENGADAAN B2B INDONESIA')
            ->assertSee('Pratinjau Lembar Komparasi')
            ->assertSee('Alur pencarian yang terstandarisasi')
            ->assertSee('Jelajahi berdasarkan kategori')
            ->assertSee('Mulai gratis. Upgrade saat Anda butuh data lengkap.')
            ->assertSee('Pertanyaan yang sering diajukan');
    }

    public function test_landing_page_search_bar_points_to_discover_index(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee(route('discover.index'))
            ->assertSee('Cari');
    }

    public function test_discover_page_handles_string_query_parameters(): void
    {
        $response = $this->get(route('discover.index', ['category' => 'Packaging Manufacturer']));
        $response->assertOk();

        $responseLocation = $this->get(route('discover.index', ['location' => 'Indonesia']));
        $responseLocation->assertOk();

        $responseCert = $this->get(route('discover.index', ['certification' => 'ISO 9001']));
        $responseCert->assertOk();
    }
}
