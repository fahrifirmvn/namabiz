<?php

namespace Tests\Feature;

use App\Models\ProcurementCollection;
use App\Models\SavedSupplier;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavedSupplierAndCollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_saved_suppliers_page(): void
    {
        $response = $this->get(route('saved.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_saved_suppliers_page(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create(['name' => 'PT Pasokan Unggul']);

        SavedSupplier::create([
            'user_id' => $user->id,
            'supplier_id' => $supplier->id,
        ]);

        $response = $this->actingAs($user)->get(route('saved.index'));

        $response->assertOk()
            ->assertSee('Pemasok Tersimpan & Proyek Pengadaan')
            ->assertSee('PT Pasokan Unggul');
    }

    public function test_user_can_create_procurement_collection(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('collections.store'), [
            'name' => 'Proyek Kemasan Kopi 2026',
            'description' => 'Evaluasi vendor kemasan pouch dan kotak karton',
        ]);

        $collection = ProcurementCollection::where('name', 'Proyek Kemasan Kopi 2026')->first();
        $this->assertNotNull($collection);
        $this->assertEquals($user->id, $collection->user_id);

        $response->assertRedirect(route('saved.index', ['collection' => $collection->id]))
            ->assertSessionHas('success');
    }

    public function test_user_can_update_own_procurement_collection(): void
    {
        $user = User::factory()->create();
        $collection = ProcurementCollection::create([
            'user_id' => $user->id,
            'name' => 'Nama Proyek Lama',
        ]);

        $response = $this->actingAs($user)->put(route('collections.update', $collection), [
            'name' => 'Nama Proyek Diperbarui',
            'description' => 'Deskripsi baru',
        ]);

        $response->assertRedirect(route('saved.index', ['collection' => $collection->id]))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('procurement_collections', [
            'id' => $collection->id,
            'name' => 'Nama Proyek Diperbarui',
        ]);
    }

    public function test_user_cannot_update_others_procurement_collection(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();

        $collection = ProcurementCollection::create([
            'user_id' => $owner->id,
            'name' => 'Proyek Rahasia',
        ]);

        $response = $this->actingAs($stranger)->put(route('collections.update', $collection), [
            'name' => 'Dibajak',
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_delete_own_procurement_collection_and_suppliers_remain(): void
    {
        $user = User::factory()->create();
        $collection = ProcurementCollection::create([
            'user_id' => $user->id,
            'name' => 'Proyek Dihapus',
        ]);

        $supplier = Supplier::factory()->create();
        $saved = SavedSupplier::create([
            'user_id' => $user->id,
            'supplier_id' => $supplier->id,
            'collection_id' => $collection->id,
        ]);

        $response = $this->actingAs($user)->delete(route('collections.destroy', $collection));

        $response->assertRedirect(route('saved.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('procurement_collections', [
            'id' => $collection->id,
        ]);

        // Verifikasi vendor masih tersimpan namun collection_id menjadi null
        $this->assertDatabaseHas('saved_suppliers', [
            'id' => $saved->id,
            'collection_id' => null,
        ]);
    }

    public function test_user_can_move_saved_supplier_to_collection(): void
    {
        $user = User::factory()->create();
        $collection = ProcurementCollection::create([
            'user_id' => $user->id,
            'name' => 'Proyek Target',
        ]);

        $supplier = Supplier::factory()->create();
        $saved = SavedSupplier::create([
            'user_id' => $user->id,
            'supplier_id' => $supplier->id,
            'collection_id' => null,
        ]);

        $response = $this->actingAs($user)->post(route('saved.move', $saved), [
            'collection_id' => $collection->id,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('saved_suppliers', [
            'id' => $saved->id,
            'collection_id' => $collection->id,
        ]);
    }

    public function test_user_can_remove_supplier_from_saved(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $saved = SavedSupplier::create([
            'user_id' => $user->id,
            'supplier_id' => $supplier->id,
        ]);

        $response = $this->actingAs($user)->delete(route('saved.destroy', $saved));

        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('saved_suppliers', [
            'id' => $saved->id,
        ]);
    }
}
