<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'title' => $this->faker->words(3, true),
            'type' => 'pdf',
            'file_path' => null,
            'external_url' => $this->faker->url(),
            'is_premium' => false,
        ];
    }
}
