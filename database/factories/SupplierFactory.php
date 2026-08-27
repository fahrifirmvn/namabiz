<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->company();
        $countries = ['Indonesia', 'Malaysia', 'Vietnam', 'China', 'Germany', 'India'];
        $categories = ['Packaging Manufacturer', 'Raw Material Supplier', 'Distributor & Converter', 'Contract Manufacturer'];

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(100, 999),
            'logo' => null,
            'tagline' => $this->faker->catchPhrase(),
            'description' => $this->faker->sentence(),
            'country' => $this->faker->randomElement($countries),
            'city' => $this->faker->city(),
            'industry_category' => $this->faker->randomElement($categories),
            'is_verified' => $this->faker->boolean(70),
            'about' => $this->faker->paragraphs(3, true),
            'website' => $this->faker->domainName(),
            'founded_year' => $this->faker->numberBetween(1990, 2022),
            'company_size' => $this->faker->randomElement(['1-50 employees', '50-100 employees', '100-500 employees', '500-1000 employees', '1000+ employees']),
            'production_capacity' => $this->faker->numberBetween(1, 10).'M units/mo',
            'moq' => number_format($this->faker->numberBetween(500, 20000)).' units',
            'avg_lead_time' => $this->faker->numberBetween(1, 8).'-'.$this->faker->numberBetween(9, 12).' weeks',
            'carbon_footprint_report' => $this->faker->boolean(50) ? 'Available ('.$this->faker->numberBetween(2022, 2024).')' : null,
        ];
    }
}

