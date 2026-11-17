<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed master taxonomy data safe for production deployments.
     * To seed curated real B2B company profiles, run: php artisan db:seed --class=SupplierSeeder
     */
    public function run(): void
    {
        $this->call([
            IndustryCategorySeeder::class,
            CertificationSeeder::class,
        ]);
    }
}
