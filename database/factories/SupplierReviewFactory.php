<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\SupplierReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupplierReview>
 */
class SupplierReviewFactory extends Factory
{
    protected $model = SupplierReview::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'user_id' => User::factory(),
            'rating' => fake()->numberBetween(3, 5),
            'quality_rating' => fake()->numberBetween(3, 5),
            'delivery_rating' => fake()->numberBetween(3, 5),
            'service_rating' => fake()->numberBetween(3, 5),
            'title' => fake()->sentence(4),
            'comment' => fake()->paragraph(),
            'reviewer_company' => fake()->company(),
            'reviewer_role' => fake()->jobTitle(),
            'is_verified_buyer' => fake()->boolean(60),
            'supplier_reply' => null,
            'replied_at' => null,
        ];
    }
}
