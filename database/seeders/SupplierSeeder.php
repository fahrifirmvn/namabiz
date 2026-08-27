<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $iso9001 = Certification::where('name', 'ISO 9001:2015')->first();
        $iso14001 = Certification::where('name', 'ISO 14001')->first();
        $fssc22000 = Certification::where('name', 'FSSC 22000')->first();
        $haccp = Certification::where('name', 'HACCP')->first();

        $named = [
            [
                'supplier' => [
                    'name' => 'Atlas Packaging',
                    'tagline' => 'Premium Packaging Solutions',
                    'description' => 'Tier-1 manufacturer specializing in sustainable corrugated solutions.',
                    'country' => 'Indonesia',
                    'city' => 'Jakarta',
                    'industry_category' => 'Packaging Manufacturer',
                    'is_verified' => true,
                    'about' => "Atlas Packaging is a leading provider of innovative and sustainable packaging solutions. With over two decades of industry experience, we specialize in developing robust industrial packaging and tailored consumer-facing designs that prioritize both durability and environmental responsibility. Our facilities are equipped with state-of-the-art machinery capable of high-volume production while maintaining stringent quality control standards.",
                    'website' => 'atlas-pkg.com',
                    'founded_year' => 1998,
                    'company_size' => '500-1000 employees',
                    'production_capacity' => '2.5M units/mo',
                    'moq' => '10,000 units',
                    'avg_lead_time' => '4-6 weeks',
                    'carbon_footprint_report' => 'Available (2023)',
                ],
                'tags' => ['PET Bottles', 'Food Containers', 'Custom Molding'],
                'certifications' => [
                    [$iso9001, 'verified', false],
                    [$iso14001, 'verified', true],
                ],
                'documents' => [
                    ['title' => 'Company Profile', 'type' => 'pdf', 'is_premium' => false],
                    ['title' => 'Product Catalog Q3 2024', 'type' => 'pdf', 'is_premium' => true],
                    ['title' => 'Technical Datasheet', 'type' => 'pdf', 'is_premium' => true],
                    ['title' => 'Full Brochure', 'type' => 'pdf', 'is_premium' => true],
                ],
            ],
            [
                'supplier' => [
                    'name' => 'Nusantara Plastics Hub',
                    'tagline' => 'Flexible Packaging Distributor',
                    'description' => 'Distributor & converter of flexible film and shrink wrap solutions.',
                    'country' => 'Indonesia',
                    'city' => 'Surabaya',
                    'industry_category' => 'Distributor & Converter',
                    'is_verified' => false,
                    'about' => 'Nusantara Plastics Hub distributes and converts flexible packaging materials for FMCG and industrial clients across Southeast Asia.',
                    'website' => 'nusantaraplastics.id',
                    'founded_year' => 2005,
                    'company_size' => '100-500 employees',
                    'production_capacity' => '1.2M units/mo',
                    'moq' => '5,000 units',
                    'avg_lead_time' => '3-4 weeks',
                    'carbon_footprint_report' => null,
                ],
                'tags' => ['Flexible Film', 'Shrink Wrap'],
                'certifications' => [
                    [$iso9001, 'verified', false],
                    [$iso14001, 'not_verified', true],
                ],
                'documents' => [
                    ['title' => 'Company Profile', 'type' => 'pdf', 'is_premium' => false],
                    ['title' => 'Product Catalog', 'type' => 'pdf', 'is_premium' => true],
                ],
            ],
            [
                'supplier' => [
                    'name' => 'Indo-Tech Solutions',
                    'tagline' => 'Precision Component Manufacturing',
                    'description' => 'Contract manufacturer for electronics and industrial components.',
                    'country' => 'Indonesia',
                    'city' => 'Bandung',
                    'industry_category' => 'Contract Manufacturer',
                    'is_verified' => false,
                    'about' => 'Indo-Tech Solutions provides precision manufacturing services for electronics enclosures and industrial components.',
                    'website' => 'indo-tech.co.id',
                    'founded_year' => 2012,
                    'company_size' => '50-100 employees',
                    'production_capacity' => '800K units/mo',
                    'moq' => '1,000 units',
                    'avg_lead_time' => '2-3 weeks',
                    'carbon_footprint_report' => null,
                ],
                'tags' => ['Injection Molding', 'CNC Machining'],
                'certifications' => [
                    [$iso9001, 'partial', false],
                ],
                'documents' => [
                    ['title' => 'Company Profile', 'type' => 'web_link', 'is_premium' => false],
                ],
            ],
            [
                'supplier' => [
                    'name' => 'Global Foundries Manufacturing',
                    'tagline' => 'Tier 1 Metal Manufacturer',
                    'description' => 'Large-scale metal casting and foundry operations.',
                    'country' => 'United States',
                    'city' => 'Austin',
                    'industry_category' => 'Raw Material Supplier',
                    'is_verified' => true,
                    'about' => 'Global Foundries Manufacturing operates multiple casting facilities providing precision metal components to the automotive and aerospace industries.',
                    'website' => 'globalfoundries-mfg.com',
                    'founded_year' => 2009,
                    'company_size' => '10,000+ employees',
                    'production_capacity' => '5M units/mo',
                    'moq' => '20,000 units',
                    'avg_lead_time' => '6-8 weeks',
                    'carbon_footprint_report' => 'Available (2024)',
                ],
                'tags' => ['Metal Casting', 'CNC Machining', 'Forging'],
                'certifications' => [
                    [$iso9001, 'verified', false],
                    [$iso14001, 'verified', true],
                    [$haccp, 'not_verified', true],
                ],
                'documents' => [
                    ['title' => 'Company Profile', 'type' => 'pdf', 'is_premium' => false],
                    ['title' => 'Audit Report', 'type' => 'pdf', 'is_premium' => true],
                ],
            ],
            [
                'supplier' => [
                    'name' => 'Apex Manufacturing',
                    'tagline' => 'Advanced Electronics Manufacturing',
                    'description' => 'Electronics assembly and manufacturing services.',
                    'country' => 'China',
                    'city' => 'Shenzhen',
                    'industry_category' => 'Contract Manufacturer',
                    'is_verified' => true,
                    'about' => 'Apex Manufacturing delivers electronics assembly and PCB manufacturing at scale for global OEM clients.',
                    'website' => 'apex-mfg.cn',
                    'founded_year' => 2015,
                    'company_size' => '1,000-5,000 employees',
                    'production_capacity' => '3M units/mo',
                    'moq' => '2,000 units',
                    'avg_lead_time' => '3-5 weeks',
                    'carbon_footprint_report' => 'Partial',
                ],
                'tags' => ['PCB Assembly', 'Electronics Manufacturing'],
                'certifications' => [
                    [$iso9001, 'verified', false],
                    [$fssc22000, 'not_verified', true],
                ],
                'documents' => [
                    ['title' => 'Company Profile', 'type' => 'pdf', 'is_premium' => false],
                    ['title' => 'Product Catalog', 'type' => 'pdf', 'is_premium' => true],
                    ['title' => 'Audit Report', 'type' => 'pdf', 'is_premium' => true],
                ],
            ],
        ];

        foreach ($named as $entry) {
            $this->createSupplier($entry['supplier'], $entry['tags'], $entry['certifications'], $entry['documents']);
        }

        // Fill up to a realistic catalogue size with randomised suppliers.
        $tagPool = ['Corrugated Boxes', 'Custom Structural Design', 'Offset Printing', 'Raw Resins', 'Steel Coils', 'Glass Bottles', 'Aluminum Cans', 'Labels & Sleeves'];
        $certifications = [$iso9001, $iso14001, $fssc22000, $haccp];

        Supplier::factory(10)->create()->each(function (Supplier $supplier) use ($tagPool, $certifications) {
            foreach ((array) array_rand(array_flip($tagPool), rand(2, 3)) as $tag) {
                $supplier->tags()->create(['tag_name' => $tag]);
            }

            foreach ($certifications as $index => $certification) {
                if (! $certification || rand(0, 1) === 0) {
                    continue;
                }

                $supplier->certifications()->create([
                    'certification_id' => $certification->id,
                    'status' => collect(['verified', 'not_verified', 'partial'])->random(),
                    'is_premium_field' => $index !== 0,
                ]);
            }

            $supplier->documents()->createMany([
                ['title' => 'Company Profile', 'type' => 'pdf', 'is_premium' => false, 'file_path' => $this->storePlaceholderFile($supplier->name, 'Company Profile')],
                ['title' => 'Product Catalog', 'type' => 'pdf', 'is_premium' => true, 'file_path' => $this->storePlaceholderFile($supplier->name, 'Product Catalog')],
            ]);
        });
    }

    private function createSupplier(array $attributes, array $tags, array $certifications, array $documents): void
    {
        $baseSlug = Str::slug($attributes['name']);
        $slug = $baseSlug;
        $suffix = 1;

        while (Supplier::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-".++$suffix;
        }

        $attributes['slug'] = $slug;
        $attributes['logo'] = null;

        $supplier = Supplier::create($attributes);

        foreach ($tags as $tag) {
            $supplier->tags()->create(['tag_name' => $tag]);
        }

        foreach ($certifications as [$certification, $status, $isPremiumField]) {
            if (! $certification) {
                continue;
            }

            $supplier->certifications()->create([
                'certification_id' => $certification->id,
                'status' => $status,
                'is_premium_field' => $isPremiumField,
            ]);
        }

        foreach ($documents as $document) {
            $document['file_path'] = $this->storePlaceholderFile($attributes['name'], $document['title']);
            $supplier->documents()->create($document);
        }
    }

    private function storePlaceholderFile(string $supplierName, string $documentTitle): string
    {
        $path = 'documents/'.Str::slug("{$supplierName}-{$documentTitle}").'.txt';
        Storage::disk('public')->put($path, "Placeholder file for \"{$documentTitle}\" ({$supplierName}).");

        return $path;
    }
}
