<?php

namespace Tests\Feature;

use App\Models\IndustryCategory;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_categories(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_categories(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get(route('admin.categories.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_view_categories_list(): void
    {
        $admin = User::factory()->admin()->create();
        $category = IndustryCategory::create([
            'name' => 'Packaging Manufacturer',
            'slug' => 'packaging-manufacturer',
            'description' => 'Pabrik kemasan',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.categories.index'));

        $response->assertOk()
            ->assertSee('Master Data Kategori Industri')
            ->assertSee($category->name);
    }

    public function test_admin_can_view_create_category_form(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.categories.create'));

        $response->assertOk()
            ->assertSee('Tambah Kategori Industri Baru')
            ->assertSee(route('admin.categories.store'));
    }

    public function test_admin_can_store_new_category(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Textile & Garment',
            'description' => 'Produsen tekstil, garmen, dan serat benang',
        ]);

        $response->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('industry_categories', [
            'name' => 'Textile & Garment',
            'slug' => 'textile-garment',
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = IndustryCategory::create([
            'name' => 'Old Category Name',
            'slug' => 'old-category-name',
        ]);
        $supplier = Supplier::factory()->create([
            'industry_category' => 'Old Category Name',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'New Category Name',
            'description' => 'Updated category description',
        ]);

        $response->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('industry_categories', [
            'id' => $category->id,
            'name' => 'New Category Name',
            'slug' => 'new-category-name',
        ]);

        // Verifikasi bahwa supplier ikut tersinkronisasi
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'industry_category' => 'New Category Name',
        ]);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = IndustryCategory::create([
            'name' => 'Category To Delete',
            'slug' => 'category-to-delete',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('industry_categories', [
            'id' => $category->id,
        ]);
    }
}
