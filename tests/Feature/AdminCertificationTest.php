<?php

namespace Tests\Feature;

use App\Models\Certification;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCertificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_certifications(): void
    {
        $response = $this->get(route('admin.certifications.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_certifications(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get(route('admin.certifications.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_view_certifications_list(): void
    {
        $admin = User::factory()->admin()->create();
        $certification = Certification::create([
            'name' => 'ISO 9001:2015',
            'category' => 'Quality Management',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.certifications.index'));

        $response->assertOk()
            ->assertSee('Master Data Sertifikasi Standar')
            ->assertSee($certification->name);
    }

    public function test_admin_can_view_create_certification_form(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.certifications.create'));

        $response->assertOk()
            ->assertSee('Tambah Sertifikasi Standar Baru')
            ->assertSee(route('admin.certifications.store'));
    }

    public function test_admin_can_store_new_certification(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.certifications.store'), [
            'name' => 'Halal MUI BPJPH',
            'category' => 'Food & Product Safety',
        ]);

        $response->assertRedirect(route('admin.certifications.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('certifications', [
            'name' => 'Halal MUI BPJPH',
            'category' => 'Food & Product Safety',
        ]);
    }

    public function test_admin_can_update_certification(): void
    {
        $admin = User::factory()->admin()->create();
        $certification = Certification::create([
            'name' => 'Old Cert Name',
            'category' => 'General',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.certifications.update', $certification), [
            'name' => 'Updated Cert Name',
            'category' => 'Quality Management',
        ]);

        $response->assertRedirect(route('admin.certifications.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('certifications', [
            'id' => $certification->id,
            'name' => 'Updated Cert Name',
            'category' => 'Quality Management',
        ]);
    }

    public function test_admin_can_delete_certification(): void
    {
        $admin = User::factory()->admin()->create();
        $certification = Certification::create([
            'name' => 'Cert To Delete',
            'category' => 'Environmental',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.certifications.destroy', $certification));

        $response->assertRedirect(route('admin.certifications.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('certifications', [
            'id' => $certification->id,
        ]);
    }

    public function test_admin_can_sync_supplier_certifications(): void
    {
        $admin = User::factory()->admin()->create();
        $supplier = Supplier::factory()->create();
        $cert1 = Certification::create(['name' => 'ISO 9001', 'category' => 'Quality']);
        $cert2 = Certification::create(['name' => 'Halal', 'category' => 'Safety']);

        $response = $this->actingAs($admin)->post(route('admin.suppliers.certifications.sync', $supplier), [
            'certifications' => [
                $cert1->id => [
                    'active' => 1,
                    'status' => 'verified',
                    'is_premium_field' => 0,
                ],
                $cert2->id => [
                    'active' => 1,
                    'status' => 'partial',
                    'is_premium_field' => 1,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.suppliers.edit', $supplier))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('supplier_certifications', [
            'supplier_id' => $supplier->id,
            'certification_id' => $cert1->id,
            'status' => 'verified',
            'is_premium_field' => false,
        ]);

        $this->assertDatabaseHas('supplier_certifications', [
            'supplier_id' => $supplier->id,
            'certification_id' => $cert2->id,
            'status' => 'partial',
            'is_premium_field' => true,
        ]);
    }
}
