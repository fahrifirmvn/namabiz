<?php

namespace Tests\Feature;

use App\Models\Certification;
use App\Models\Document;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSupplierTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_admin_portal(): void
    {
        $response = $this->get(route('admin.suppliers.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_admin_portal(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.suppliers.index'));

        $response->assertForbidden();
    }

    public function test_admin_user_can_view_suppliers_list(): void
    {
        $admin = User::factory()->admin()->create();
        $supplier = Supplier::factory()->create(['name' => 'PT Mitra Sejati']);

        $response = $this->actingAs($admin)->get(route('admin.suppliers.index'));

        $response->assertOk()
            ->assertSee('Manajemen Pemasok')
            ->assertSee($supplier->name);
    }

    public function test_admin_user_can_view_create_supplier_form(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.suppliers.create'));

        $response->assertOk()
            ->assertSee('Tambah Pemasok Baru')
            ->assertSee(route('admin.suppliers.store'));
    }

    public function test_admin_user_can_store_new_supplier(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.suppliers.store'), [
            'name' => 'PT Prima Kemas Nusantara',
            'tagline' => 'Solusi Kemasan Terlengkap',
            'description' => 'Pabrik kemasan terpercaya di Jawa Timur',
            'about' => 'Berdiri sejak 2015 melayani ratusan klien UMKM dan Korporat.',
            'country' => 'Indonesia',
            'city' => 'Sidoarjo',
            'industry_category' => 'Packaging Manufacturer',
            'whatsapp' => '081298765432',
            'email' => 'sales@primakemas.co.id',
            'website' => 'https://primakemas.co.id',
            'moq' => '5.000 unit',
            'production_capacity' => '500.000 unit / bulan',
            'avg_lead_time' => '14 hari',
            'is_verified' => 1,
        ]);

        $supplier = Supplier::where('name', 'PT Prima Kemas Nusantara')->first();
        $this->assertNotNull($supplier);

        $response->assertRedirect(route('admin.suppliers.edit', $supplier))
            ->assertSessionHas('success');

        $this->assertEquals('pt-prima-kemas-nusantara', $supplier->slug);
        $this->assertEquals('081298765432', $supplier->whatsapp);
        $this->assertEquals('sales@primakemas.co.id', $supplier->email);
        $this->assertTrue($supplier->is_verified);
    }

    public function test_admin_user_can_view_edit_supplier_form(): void
    {
        $admin = User::factory()->admin()->create();
        $supplier = Supplier::factory()->create(['name' => 'PT Sumber Kemas']);

        $response = $this->actingAs($admin)->get(route('admin.suppliers.edit', $supplier));

        $response->assertOk()
            ->assertSee($supplier->name)
            ->assertSee(route('admin.suppliers.update', $supplier));
    }

    public function test_admin_user_can_update_supplier(): void
    {
        $admin = User::factory()->admin()->create();
        $supplier = Supplier::factory()->create([
            'name' => 'Old Supplier Name',
            'country' => 'Indonesia',
            'industry_category' => 'Packaging',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.suppliers.update', $supplier), [
            'name' => 'Updated Supplier Name',
            'tagline' => 'Best in packaging',
            'description' => 'Updated description',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'industry_category' => 'Packaging Manufacturer',
            'whatsapp' => '08111222333',
            'email' => 'contact@updated-supplier.com',
            'website' => 'https://updated-supplier.com',
            'moq' => '5,000 units',
            'production_capacity' => '1M units/mo',
            'avg_lead_time' => '2-4 weeks',
            'is_verified' => 1,
        ]);

        $response->assertRedirect(route('admin.suppliers.edit', $supplier))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Updated Supplier Name',
            'city' => 'Jakarta',
            'whatsapp' => '08111222333',
            'email' => 'contact@updated-supplier.com',
            'is_verified' => 1,
        ]);
    }

    public function test_admin_user_can_delete_supplier(): void
    {
        $admin = User::factory()->admin()->create();
        $supplier = Supplier::factory()->create([
            'name' => 'Supplier To Delete',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.suppliers.destroy', $supplier));

        $response->assertRedirect(route('admin.suppliers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('suppliers', [
            'id' => $supplier->id,
        ]);
    }

    public function test_admin_user_can_upload_and_delete_supplier_document(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $supplier = Supplier::factory()->create();

        $pdfFile = UploadedFile::fake()->create('company_profile.pdf', 500, 'application/pdf');

        // Test Upload Document
        $uploadResponse = $this->actingAs($admin)->post(route('admin.suppliers.documents.store', $supplier), [
            'title' => 'Katalog Produk & Profil 2025',
            'type' => 'PDF',
            'file' => $pdfFile,
            'is_premium' => 1,
        ]);

        $uploadResponse->assertRedirect(route('admin.suppliers.edit', $supplier))
            ->assertSessionHas('success');

        $document = Document::where('supplier_id', $supplier->id)->first();
        $this->assertNotNull($document);
        $this->assertEquals('Katalog Produk & Profil 2025', $document->title);
        $this->assertTrue($document->is_premium);
        Storage::disk('public')->assertExists($document->file_path);

        // Test Delete Document
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.suppliers.documents.destroy', [
            'supplier' => $supplier,
            'document' => $document,
        ]));

        $deleteResponse->assertRedirect(route('admin.suppliers.edit', $supplier))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('documents', [
            'id' => $document->id,
        ]);
        Storage::disk('public')->assertMissing($document->file_path);
    }

    public function test_admin_can_create_supplier_in_one_step_with_tags_certifications_and_documents(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $certification = Certification::create([
            'name' => 'ISO 9001:2015',
            'category' => 'Quality Management',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.suppliers.store'), [
            'name' => 'PT Satu Langkah Maju',
            'country' => 'Indonesia',
            'industry_category' => 'Packaging Manufacturer',
            'tags' => 'Rotogravure, Standing Pouch, rotogravure',
            'certifications' => [
                $certification->id => [
                    'active' => '1',
                    'status' => 'verified',
                    'is_premium_field' => '1',
                ],
            ],
            'documents' => [
                UploadedFile::fake()->create('company_profile.pdf', 200, 'application/pdf'),
                UploadedFile::fake()->create('spesifikasi-teknis_2026.xlsx', 150),
            ],
            'doc_titles' => [
                0 => 'Profil Perusahaan Resmi',
                1 => '',
            ],
            'doc_premium' => [
                1 => '1',
            ],
        ]);

        $supplier = Supplier::where('slug', 'pt-satu-langkah-maju')->firstOrFail();

        $response->assertRedirect(route('admin.suppliers.edit', $supplier))
            ->assertSessionHas('success');

        $this->assertSame(['Rotogravure', 'Standing Pouch'], $supplier->tags()->pluck('tag_name')->all());

        $this->assertDatabaseHas('supplier_certifications', [
            'supplier_id' => $supplier->id,
            'certification_id' => $certification->id,
            'status' => 'verified',
            'is_premium_field' => 1,
        ]);

        $documents = $supplier->documents()->orderBy('id')->get();
        $this->assertCount(2, $documents);
        $this->assertSame('Profil Perusahaan Resmi', $documents[0]->title);
        $this->assertSame('PDF', $documents[0]->type);
        $this->assertFalse($documents[0]->is_premium);
        $this->assertSame('Spesifikasi Teknis 2026', $documents[1]->title);
        $this->assertSame('XLSX', $documents[1]->type);
        $this->assertTrue($documents[1]->is_premium);
    }

    public function test_admin_can_clear_nullable_fields_and_remove_logo_on_update(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $oldLogoPath = UploadedFile::fake()->image('logo.png')->store('suppliers', 'public');

        $supplier = Supplier::factory()->create([
            'name' => 'PT Bersih Data',
            'country' => 'Indonesia',
            'city' => 'Surabaya',
            'tagline' => 'Tagline Lama',
            'whatsapp' => '08123456789',
            'industry_category' => 'Packaging',
            'logo' => $oldLogoPath,
        ]);
        $supplier->tags()->create(['tag_name' => 'Tag Lama']);

        $response = $this->actingAs($admin)->put(route('admin.suppliers.update', $supplier), [
            'name' => 'PT Bersih Data',
            'country' => 'Indonesia',
            'city' => '',
            'tagline' => '',
            'whatsapp' => '',
            'industry_category' => 'Packaging',
            'remove_logo' => '1',
            'tags' => 'Injeksi Plastik, Blow Molding',
        ]);

        $response->assertRedirect(route('admin.suppliers.edit', $supplier));

        $supplier->refresh();
        $this->assertNull($supplier->city);
        $this->assertNull($supplier->tagline);
        $this->assertNull($supplier->whatsapp);
        $this->assertNull($supplier->logo);
        Storage::disk('public')->assertMissing($oldLogoPath);
        $this->assertSame(['Injeksi Plastik', 'Blow Molding'], $supplier->tags()->pluck('tag_name')->all());
    }

    public function test_admin_can_toggle_supplier_verification_badge(): void
    {
        $admin = User::factory()->admin()->create();
        $supplier = Supplier::factory()->create(['is_verified' => false]);

        $this->actingAs($admin)
            ->from(route('admin.suppliers.index'))
            ->patch(route('admin.suppliers.verify', $supplier))
            ->assertRedirect(route('admin.suppliers.index'));

        $this->assertTrue($supplier->fresh()->is_verified);

        $this->actingAs($admin)
            ->patch(route('admin.suppliers.verify', $supplier));

        $this->assertFalse($supplier->fresh()->is_verified);
    }

    public function test_admin_can_filter_suppliers_by_category_and_verification_status(): void
    {
        $admin = User::factory()->admin()->create();
        Supplier::factory()->create([
            'name' => 'PT Kemas Terverifikasi',
            'industry_category' => 'Packaging Manufacturer',
            'is_verified' => true,
        ]);
        Supplier::factory()->create([
            'name' => 'PT Tekstil Standar',
            'industry_category' => 'Textile Mill',
            'is_verified' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.suppliers.index', [
            'category' => 'Packaging Manufacturer',
            'status' => 'verified',
        ]));

        $response->assertOk()
            ->assertSee('PT Kemas Terverifikasi')
            ->assertDontSee('PT Tekstil Standar');
    }

    public function test_admin_can_bulk_upload_documents_and_rejects_invalid_formats(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $supplier = Supplier::factory()->create();

        $this->actingAs($admin)->post(route('admin.suppliers.documents.bulk', $supplier), [
            'documents' => [
                UploadedFile::fake()->create('malware.exe', 100),
            ],
        ])->assertSessionHasErrors('documents.0');

        $response = $this->actingAs($admin)->post(route('admin.suppliers.documents.bulk', $supplier), [
            'documents' => [
                UploadedFile::fake()->create('katalog_2026.pdf', 300, 'application/pdf'),
                UploadedFile::fake()->create('daftar_harga.docx', 200),
            ],
            'doc_titles' => [
                0 => 'Katalog Lengkap 2026',
            ],
        ]);

        $response->assertRedirect(route('admin.suppliers.edit', $supplier))
            ->assertSessionHas('tab', 'dokumen');

        $this->assertSame(2, $supplier->documents()->count());
    }

    public function test_supplier_profile_completeness_scores_filled_and_missing_sections(): void
    {
        $sparse = Supplier::factory()->create([
            'logo' => null,
            'tagline' => null,
            'about' => '',
            'description' => null,
            'whatsapp' => null,
            'email' => null,
            'website' => null,
            'city' => null,
            'moq' => null,
            'production_capacity' => null,
            'avg_lead_time' => null,
            'company_size' => null,
            'founded_year' => null,
        ]);

        $result = $sparse->profileCompleteness();

        $this->assertSame(0, $result['percent']);
        $this->assertContains('Logo', $result['missing']);
        $this->assertContains('Kemampuan inti (tag)', $result['missing']);
        $this->assertContains('Dokumen', $result['missing']);
    }
}
