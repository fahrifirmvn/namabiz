<?php

namespace Database\Seeders;

use App\Models\Certification;
use Illuminate\Database\Seeder;

class CertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $certifications = [
            ['name' => 'ISO 9001:2015', 'category' => 'Quality Management'],
            ['name' => 'ISO 14001', 'category' => 'Environmental'],
            ['name' => 'ISO 45001', 'category' => 'Occupational Health & Safety'],
            ['name' => 'ISO 22000', 'category' => 'Food Safety'],
            ['name' => 'FSSC 22000', 'category' => 'Food Safety'],
            ['name' => 'HACCP', 'category' => 'Food Safety'],
            ['name' => 'Halal BPJPH / MUI', 'category' => 'Halal Assurance'],
        ];

        foreach ($certifications as $certification) {
            Certification::firstOrCreate(['name' => $certification['name']], $certification);
        }
    }
}
