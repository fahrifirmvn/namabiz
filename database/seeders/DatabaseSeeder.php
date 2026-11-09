<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Free User',
            'email' => 'free@example.com',
        ]);

        User::factory()->create([
            'name' => 'Premium User',
            'email' => 'premium@example.com',
            'subscription_tier' => 'premium',
            'premium_expires_at' => now()->addYear(),
        ]);

        User::factory()->admin()->create([
            'name' => 'Admin Namabiz',
            'email' => 'admin@namabiz.com',
        ]);

        $this->call([
            CertificationSeeder::class,
            SupplierSeeder::class,
        ]);
    }
}
