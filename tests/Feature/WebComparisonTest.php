<?php

namespace Tests\Feature;

use App\Livewire\ComparisonTable;
use App\Models\Certification;
use App\Models\Document;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class WebComparisonTest extends TestCase
{
    use RefreshDatabase;

    public function test_comparison_index_page_can_be_rendered(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'PT Mitra Pasokan']);

        $response = $this->get(route('compare.index', ['suppliers' => [$supplier->id]]));

        $response->assertOk()
            ->assertSee('PT Mitra Pasokan');
    }

    public function test_guest_is_redirected_to_upgrade_when_attempting_to_download_pdf(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->get(route('compare.download', ['suppliers' => [$supplier->id]]));

        $response->assertRedirect(route('upgrade'));
    }

    public function test_free_user_is_redirected_to_upgrade_when_attempting_to_download_pdf(): void
    {
        $freeUser = User::factory()->create([
            'subscription_tier' => 'free',
        ]);
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($freeUser)->get(route('compare.download', ['suppliers' => [$supplier->id]]));

        $response->assertRedirect(route('upgrade'));
    }

    public function test_premium_user_can_download_comparison_pdf(): void
    {
        $premiumUser = User::factory()->create([
            'subscription_tier' => 'premium',
            'premium_expires_at' => now()->addYear(),
        ]);

        $supplier1 = Supplier::factory()->create([
            'name' => 'PT Manufaktur Satu',
            'whatsapp' => '08123456789',
            'email' => 'sales@satu.co.id',
            'moq' => '10.000 unit',
        ]);
        $supplier2 = Supplier::factory()->create([
            'name' => 'PT Manufaktur Dua',
            'whatsapp' => '08198765432',
            'email' => 'halo@dua.co.id',
            'moq' => '5.000 unit',
        ]);

        $response = $this->actingAs($premiumUser)->get(route('compare.download', [
            'suppliers' => [$supplier1->id, $supplier2->id],
        ]));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('namabiz-laporan-komparasi-pemasok.pdf', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_user_can_download_genuine_document_pdf(): void
    {
        Storage::fake('public');

        $supplier = Supplier::factory()->create(['name' => 'PT Sumber Kertas']);
        $filePath = 'documents/pt-sumber-kertas-katalog.pdf';
        Storage::disk('public')->put($filePath, '%PDF-1.7 sample genuine pdf binary');

        $document = Document::create([
            'supplier_id' => $supplier->id,
            'title' => 'Katalog Produk 2025',
            'type' => 'PDF',
            'file_path' => $filePath,
            'is_premium' => false,
        ]);

        $response = $this->get(route('documents.download', $document));

        $response->assertOk();
        $this->assertStringContainsString('Katalog Produk 2025', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_comparison_table_matches_versioned_iso_certification_and_filters_identical_rows(): void
    {
        $iso = Certification::create(['name' => 'ISO 9001:2015', 'category' => 'Quality Management']);

        $supplierA = Supplier::factory()->create(['founded_year' => 2010, 'company_size' => '100-500 karyawan']);
        $supplierB = Supplier::factory()->create(['founded_year' => 2010, 'company_size' => '500-1000 karyawan']);

        $supplierA->certifications()->create([
            'certification_id' => $iso->id,
            'status' => 'verified',
            'is_premium_field' => false,
        ]);

        Livewire::test(ComparisonTable::class, ['supplierIds' => [$supplierA->id, $supplierB->id]])
            ->assertSee('Bersertifikat aktif')
            ->assertSee('Tahun Berdiri')
            ->call('toggleDifferences')
            ->assertDontSee('Tahun Berdiri')
            ->assertSee('Ukuran Perusahaan');
    }
}
