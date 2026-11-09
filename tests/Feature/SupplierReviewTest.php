<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\SupplierReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_submit_supplier_review(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->post(route('suppliers.reviews.store', $supplier->slug), [
            'rating' => 5,
            'quality_rating' => 5,
            'delivery_rating' => 4,
            'service_rating' => 5,
            'title' => 'Vendor Sangat Bagus',
            'comment' => 'Kualitas produk sangat presisi dan delivery tepat waktu.',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseEmpty('supplier_reviews');
    }

    public function test_authenticated_user_can_submit_review(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($user)->post(route('suppliers.reviews.store', $supplier->slug), [
            'rating' => 5,
            'quality_rating' => 5,
            'delivery_rating' => 4,
            'service_rating' => 5,
            'title' => 'Hasil cetak sangat memuaskan',
            'comment' => 'Kemasan foil standing pouch rapi, sealing kuat dan tidak ada cacat.',
            'reviewer_company' => 'PT Pangan Perkasa',
            'reviewer_role' => 'Procurement Officer',
        ]);

        $response->assertRedirect(route('suppliers.show', ['supplier' => $supplier->slug, 'tab' => 'ulasan']));
        $this->assertDatabaseHas('supplier_reviews', [
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'rating' => 5,
            'quality_rating' => 5,
            'delivery_rating' => 4,
            'service_rating' => 5,
            'title' => 'Hasil cetak sangat memuaskan',
            'reviewer_company' => 'PT Pangan Perkasa',
            'reviewer_role' => 'Procurement Officer',
            'is_verified_buyer' => false,
        ]);
    }

    public function test_premium_user_review_is_marked_as_verified_buyer(): void
    {
        $user = User::factory()->premium()->create();
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($user)->post(route('suppliers.reviews.store', $supplier->slug), [
            'rating' => 4,
            'quality_rating' => 4,
            'delivery_rating' => 4,
            'service_rating' => 5,
            'title' => 'Layanan cepat dan komunikatif',
            'comment' => 'Vendor responsif dalam pembuatan sampel awal.',
        ]);

        $response->assertRedirect(route('suppliers.show', ['supplier' => $supplier->slug, 'tab' => 'ulasan']));
        $this->assertDatabaseHas('supplier_reviews', [
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'rating' => 4,
            'is_verified_buyer' => true,
        ]);
    }

    public function test_user_updating_review_updates_existing_record(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();

        $this->actingAs($user)->post(route('suppliers.reviews.store', $supplier->slug), [
            'rating' => 3,
            'quality_rating' => 3,
            'delivery_rating' => 3,
            'service_rating' => 3,
            'title' => 'Cukup memuaskan',
            'comment' => 'Ada sedikit keterlambatan di pengiriman batch pertama.',
        ]);

        $this->assertDatabaseCount('supplier_reviews', 1);

        $this->actingAs($user)->post(route('suppliers.reviews.store', $supplier->slug), [
            'rating' => 5,
            'quality_rating' => 5,
            'delivery_rating' => 5,
            'service_rating' => 5,
            'title' => 'Sangat memuaskan setelah evaluasi',
            'comment' => 'Vendor memperbaiki lead time dan batch kedua datang tepat waktu.',
        ]);

        $this->assertDatabaseCount('supplier_reviews', 1);
        $this->assertDatabaseHas('supplier_reviews', [
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'rating' => 5,
            'title' => 'Sangat memuaskan setelah evaluasi',
        ]);
    }

    public function test_user_can_delete_own_review(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $review = SupplierReview::factory()->create([
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('suppliers.reviews.destroy', [$supplier->slug, $review]));

        $response->assertRedirect(route('suppliers.show', ['supplier' => $supplier->slug, 'tab' => 'ulasan']));
        $this->assertDatabaseMissing('supplier_reviews', ['id' => $review->id]);
    }

    public function test_user_cannot_delete_other_users_review(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $review = SupplierReview::factory()->create([
            'supplier_id' => $supplier->id,
            'user_id' => $userA->id,
        ]);

        $response = $this->actingAs($userB)->delete(route('suppliers.reviews.destroy', [$supplier->slug, $review]));

        $response->assertForbidden();
        $this->assertDatabaseHas('supplier_reviews', ['id' => $review->id]);
    }

    public function test_admin_can_post_official_reply_to_review(): void
    {
        $admin = User::factory()->admin()->create();
        $supplier = Supplier::factory()->create();
        $review = SupplierReview::factory()->create([
            'supplier_id' => $supplier->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.suppliers.reviews.reply', [$supplier, $review]), [
            'supplier_reply' => 'Terima kasih atas masukannya, kami berkomitmen menjaga mutu batch produksi Anda.',
        ]);

        $response->assertRedirect(route('admin.suppliers.edit', $supplier));
        $this->assertDatabaseHas('supplier_reviews', [
            'id' => $review->id,
            'supplier_reply' => 'Terima kasih atas masukannya, kami berkomitmen menjaga mutu batch produksi Anda.',
        ]);
        $this->assertNotNull($review->fresh()->replied_at);
    }

    public function test_admin_can_delete_any_review(): void
    {
        $admin = User::factory()->admin()->create();
        $supplier = Supplier::factory()->create();
        $review = SupplierReview::factory()->create([
            'supplier_id' => $supplier->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.suppliers.reviews.destroy', [$supplier, $review]));

        $response->assertRedirect(route('admin.suppliers.edit', $supplier));
        $this->assertDatabaseMissing('supplier_reviews', ['id' => $review->id]);
    }

    public function test_supplier_show_page_displays_reviews_and_average_rating(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'PT Pabrik Unggul']);
        $user = User::factory()->premium()->create(['name' => 'Budi Santoso']);

        SupplierReview::factory()->create([
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'rating' => 5,
            'quality_rating' => 5,
            'delivery_rating' => 4,
            'service_rating' => 5,
            'title' => 'Rekomendasi terbaik untuk kemasan eco-friendly',
            'comment' => 'Bahan ramah lingkungan bersertifikasi FSC dan lead time stabil.',
            'reviewer_company' => 'CV Organik Indonesia',
            'reviewer_role' => 'COO',
            'is_verified_buyer' => true,
            'supplier_reply' => 'Terima kasih atas kepercayaan rekan CV Organik!',
            'replied_at' => now(),
        ]);

        $response = $this->get(route('suppliers.show', $supplier->slug));

        $response->assertOk()
            ->assertSee('5.0')
            ->assertSee('1 ulasan')
            ->assertSee('Budi Santoso')
            ->assertSee('Pembeli Terverifikasi')
            ->assertSee('Rekomendasi terbaik untuk kemasan eco-friendly')
            ->assertSee('Terima kasih atas kepercayaan rekan CV Organik!');
    }
}
