<?php

namespace Tests\Feature;

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
}
