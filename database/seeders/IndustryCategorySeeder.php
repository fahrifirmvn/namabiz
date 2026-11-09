<?php

namespace Database\Seeders;

use App\Models\IndustryCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IndustryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Packaging Manufacturer',
                'description' => 'Pabrik dan produsen utama kemasan kaku, fleksibel, karton, dan botol.',
            ],
            [
                'name' => 'Distributor & Converter',
                'description' => 'Distributor resmi dan perusahaan pemotong/pengubah film dan material pasokan.',
            ],
            [
                'name' => 'Contract Manufacturer',
                'description' => 'Penyedia jasa maklon, perakitan elektronik, komponen presisi, dan formulasi.',
            ],
            [
                'name' => 'Raw Material Supplier',
                'description' => 'Pemasok bahan baku primer seperti biji plastik, logam, gulungan baja, dan bahan kimia.',
            ],
            [
                'name' => 'Spices & Coconut',
                'description' => 'Produsen dan eksportir komoditas rempah-rempah, kelapa terolah, dan hasil bumi.',
            ],
            [
                'name' => 'Logistics & Warehousing',
                'description' => 'Penyedia layanan rantai pasok terintegrasi, pergudangan dingin, dan kargo.',
            ],
            [
                'name' => 'Chemical & Ingredients',
                'description' => 'Bahan aditif makanan, perasa, pewarna industri, dan bahan kimia farmasi.',
            ],
        ];

        foreach ($categories as $cat) {
            IndustryCategory::firstOrCreate(
                ['name' => $cat['name']],
                [
                    'slug' => Str::slug($cat['name']),
                    'description' => $cat['description'],
                ]
            );
        }
    }
}
