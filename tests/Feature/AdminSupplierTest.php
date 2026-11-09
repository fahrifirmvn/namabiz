<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('Supplier Management (Admin Portal)')
            ->assertSee($supplier->name);
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
            'website' => 'https://updated-supplier.com',
            'moq' => '5,000 units',
            'production_capacity' => '1M units/mo',
            'avg_lead_time' => '2-4 weeks',
            'is_verified' => 1,
        ]);

        $response->assertRedirect(route('admin.suppliers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Updated Supplier Name',
            'city' => 'Jakarta',
            'is_verified' => 1,
        ]);
    }
}
