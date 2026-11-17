<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Supplier;
use App\Observers\CatalogCacheObserver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SupplierSeeder extends Seeder
{
    /**
     * Seed curated profiles of real Indonesian B2B manufacturing and logistics companies.
     */
    public function run(): void
    {
        $this->call([
            IndustryCategorySeeder::class,
            CertificationSeeder::class,
        ]);

        $certificationsByName = Certification::pluck('id', 'name');
        $disk = Storage::disk('public');

        foreach ($this->companies() as $company) {
            $slug = $company['slug'];
            $logoPath = null;
            $sourceLogo = database_path("seeders/assets/suppliers/{$slug}.webp");

            if (is_file($sourceLogo)) {
                $targetPath = "suppliers/{$slug}.webp";
                $disk->put($targetPath, (string) file_get_contents($sourceLogo));
                $logoPath = $targetPath;
            }

            $supplier = Supplier::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $company['name'],
                    'industry_category' => $company['industry_category'],
                    'tagline' => $company['tagline'],
                    'description' => $company['description'],
                    'about' => $company['description'],
                    'country' => 'Indonesia',
                    'city' => $company['city'],
                    'website' => $company['website'],
                    'email' => $company['email'] ?? null,
                    'whatsapp' => null,
                    'founded_year' => $company['founded_year'],
                    'company_size' => null,
                    'production_capacity' => null,
                    'moq' => null,
                    'avg_lead_time' => null,
                    'logo' => $logoPath,
                    'is_verified' => false,
                ]
            );

            $supplier->tags()->delete();
            foreach ($company['tags'] as $tagName) {
                $supplier->tags()->create(['tag_name' => $tagName]);
            }

            $supplier->certifications()->delete();
            foreach ($company['certifications'] ?? [] as $certName) {
                $certId = $certificationsByName->get($certName);
                if (! $certId) {
                    continue;
                }

                $supplier->certifications()->create([
                    'certification_id' => $certId,
                    'status' => 'partial',
                    'is_premium_field' => false,
                ]);
            }
        }

        CatalogCacheObserver::flush();
    }

    /**
     * Curated Indonesian B2B suppliers researched from official company websites.
     *
     * @return list<array<string, mixed>>
     */
    private function companies(): array
    {
        return [
            [
                'name' => 'PT Berlina Tbk',
                'slug' => 'pt-berlina-tbk',
                'industry_category' => 'Packaging Manufacturer',
                'city' => 'Cikarang, Bekasi',
                'founded_year' => 1969,
                'website' => 'https://www.berlina.co.id',
                'tagline' => 'Produsen kemasan plastik injeksi, tiup, dan tube untuk industri.',
                'description' => 'Perusahaan publik Indonesia yang memproduksi kemasan plastik rigid, tube plastik, sikat gigi, serta cetakan (mould) untuk sektor kosmetik, farmasi, makanan, dan otomotif.',
                'tags' => ['Blow Moulding', 'Injection Moulding', 'Plastic Tubes', 'Packaging Mould'],
            ],
            [
                'name' => 'PT Trias Sentosa Tbk',
                'slug' => 'pt-trias-sentosa-tbk',
                'industry_category' => 'Packaging Manufacturer',
                'city' => 'Sidoarjo',
                'founded_year' => 1979,
                'website' => 'https://www.trias-sentosa.com',
                'tagline' => 'Produsen film kemasan fleksibel BOPP dan BOPET berskala regional.',
                'description' => 'Manufaktur Biaxially Oriented Polypropylene (BOPP) dan Polyester (BOPET) film untuk aplikasi kemasan makanan, minuman, rokok, dan laminasi industri.',
                'tags' => ['BOPP Film', 'BOPET Film', 'Flexible Packaging', 'Metalized Film'],
            ],
            [
                'name' => 'PT Fajar Surya Wisesa Tbk',
                'slug' => 'pt-fajar-surya-wisesa-tbk',
                'industry_category' => 'Packaging Manufacturer',
                'city' => 'Jakarta Pusat',
                'founded_year' => 1988,
                'website' => 'https://www.fajarpaper.com',
                'tagline' => 'Produsen kertas kemasan industri berbahan baku kertas daur ulang.',
                'description' => 'Memproduksi kertas kemasan industri seperti kraft linerboard, corrugated medium paper, dan coated duplex board untuk kebutuhan kotak karton bergelombang.',
                'tags' => ['Containerboard', 'Kraft Liner', 'Corrugated Medium', 'Duplex Board'],
            ],
            [
                'name' => 'PT Suparma Tbk',
                'slug' => 'pt-suparma-tbk',
                'industry_category' => 'Packaging Manufacturer',
                'city' => 'Surabaya',
                'founded_year' => 1976,
                'website' => 'https://www.ptsuparmatbk.com',
                'email' => 'info@ptsuparmatbk.com',
                'tagline' => 'Manufaktur kertas industri, kertas tisu, dan kertas pembungkus.',
                'description' => 'Produsen kertas dan tisu berbasis di Surabaya yang memproduksi duplex, kraft, laminated wrapping kraft, serta produk tisu komersial dan rumah tangga.',
                'tags' => ['Industrial Paper', 'Duplex Board', 'Kraft Paper', 'Commercial Tissue'],
                'certifications' => ['ISO 9001:2015', 'ISO 14001'],
            ],
            [
                'name' => 'PT Indopoly Swakarsa Industry Tbk',
                'slug' => 'pt-indopoly-swakarsa-industry-tbk',
                'industry_category' => 'Packaging Manufacturer',
                'city' => 'Jakarta',
                'founded_year' => 1995,
                'website' => 'https://www.indopoly.co.id',
                'tagline' => 'Produsen BOPP dan BOPET film untuk kemasan fleksibel.',
                'description' => 'Menyuplai film kemasan fleksibel untuk industri makanan, barang konsumen, laminasi termal, serta berbagai kebutuhan kemasan industri.',
                'tags' => ['BOPP Film', 'BOPET Film', 'Food Packaging Film', 'Thermal Lamination'],
                'certifications' => ['ISO 9001:2015', 'ISO 14001', 'FSSC 22000'],
            ],
            [
                'name' => 'PT Alkindo Naratama Tbk',
                'slug' => 'pt-alkindo-naratama-tbk',
                'industry_category' => 'Distributor & Converter',
                'city' => 'Bandung Barat',
                'founded_year' => 1989,
                'website' => 'https://www.alkindo.co.id',
                'email' => 'aldopack@alkindo.co.id',
                'tagline' => 'Konverter kertas terintegrasi untuk paper tube, core, dan honeycomb.',
                'description' => 'Grup manufaktur dan konversi produk berbasis kertas daur ulang yang menghasilkan paper tube, paper core, edge protector, honeycomb, dan kemasan kertas.',
                'tags' => ['Paper Core', 'Paper Tube', 'Paper Honeycomb', 'Paper Packaging'],
                'certifications' => ['ISO 9001:2015'],
            ],
            [
                'name' => 'PT Lautan Luas Tbk',
                'slug' => 'pt-lautan-luas-tbk',
                'industry_category' => 'Distributor & Converter',
                'city' => 'Jakarta',
                'founded_year' => 1951,
                'website' => 'https://www.lautan-luas.com',
                'email' => 'info@lautan-luas.com',
                'tagline' => 'Distributor dan manufaktur bahan kimia dasar serta spesialisasi industri.',
                'description' => 'Penyedia bahan baku kimia, konversi produk pangan, pengolahan air bersih, serta layanan pendukung teknis untuk berbagai sektor manufaktur di Indonesia.',
                'tags' => ['Industrial Chemicals', 'Water Treatment', 'Food Ingredients', 'Chemical Distribution'],
                'certifications' => ['ISO 9001:2015', 'ISO 14001'],
            ],
            [
                'name' => 'PT Sat Nusapersada Tbk',
                'slug' => 'pt-sat-nusapersada-tbk',
                'industry_category' => 'Contract Manufacturer',
                'city' => 'Batam',
                'founded_year' => 1990,
                'website' => 'https://www.satnusa.com',
                'tagline' => 'Jasa manufaktur elektronik terintegrasi (EMS) dan perakitan OEM.',
                'description' => 'Penyedia layanan Electronics Manufacturing Services (EMS) di Batam yang mencakup SMT assembly, plastic injection, metal stamping, hingga perakitan produk jadi.',
                'tags' => ['Electronics Manufacturing', 'SMT Assembly', 'OEM Assembly', 'Contract Manufacturing'],
            ],
            [
                'name' => 'PT Chandra Asri Pacific Tbk',
                'slug' => 'pt-chandra-asri-pacific-tbk',
                'industry_category' => 'Raw Material Supplier',
                'city' => 'Jakarta',
                'founded_year' => 1984,
                'website' => 'https://chandra-asri.com',
                'tagline' => 'Perusahaan solusi kimia, petrokimia, dan infrastruktur industri.',
                'description' => 'Produsen petrokimia Indonesia yang memasok olefin, polietilena (PE), polipropilena (PP), stirena monomer, serta bahan baku kimia industri.',
                'tags' => ['Petrochemical', 'Polypropylene', 'Polyethylene', 'Industrial Resin'],
                'certifications' => ['ISO 9001:2015', 'ISO 14001'],
            ],
            [
                'name' => 'PT Pulau Sambu',
                'slug' => 'pt-pulau-sambu',
                'industry_category' => 'Spices & Coconut',
                'city' => 'Jakarta Utara',
                'founded_year' => 1967,
                'website' => 'https://www.sambugroup.com',
                'email' => 'info@sambu.co.id',
                'tagline' => 'Pengolahan kelapa terpadu untuk bahan pangan dan industri ekspor.',
                'description' => 'Bagian dari Sambu Group yang bergerak di industri pengolahan kelapa terpadu, menghasilkan santan, air kelapa, kelapa parut kering (desiccated coconut), dan minyak kelapa.',
                'tags' => ['Coconut Processing', 'Desiccated Coconut', 'Coconut Milk', 'Agri Exports'],
            ],
            [
                'name' => 'PT Puninar Jaya',
                'slug' => 'pt-puninar-jaya',
                'industry_category' => 'Logistics & Warehousing',
                'city' => 'Jakarta Timur',
                'founded_year' => 1969,
                'website' => 'https://www.puninar.com',
                'tagline' => 'Layanan logistik end-to-end, pergudangan, dan distribusi kargo B2B.',
                'description' => 'Dikenal sebagai Puninar Logistics, menyediakan manajemen rantai pasok terintegrasi meliputi freight forwarding, transportasi multimoda, custom clearance, dan pergudangan.',
                'tags' => ['Supply Chain Logistics', 'Warehousing', 'Freight Forwarding', 'Inland Transport'],
            ],
            [
                'name' => 'PT Kamadjaja Logistics',
                'slug' => 'pt-kamadjaja-logistics',
                'industry_category' => 'Logistics & Warehousing',
                'city' => 'Surabaya',
                'founded_year' => 1968,
                'website' => 'https://www.kamadjaja.com',
                'email' => 'info@kamadjaja.com',
                'tagline' => 'Jaringan pergudangan modern dan distribusi logistik nasional.',
                'description' => 'Menyediakan layanan logistik pihak ketiga (3PL), pusat distribusi K-LOG Park, manajemen inventaris, serta pengiriman domestik terintegrasi.',
                'tags' => ['3PL Logistics', 'Distribution Center', 'Contract Warehousing', 'Domestic Freight'],
            ],
            [
                'name' => 'PT Samudera Indonesia Tbk',
                'slug' => 'pt-samudera-indonesia-tbk',
                'industry_category' => 'Logistics & Warehousing',
                'city' => 'Jakarta Barat',
                'founded_year' => 1964,
                'website' => 'https://www.samudera.id',
                'email' => 'corporate.secretary@samudera.id',
                'tagline' => 'Grup pelayaran kargo, operator pelabuhan, dan logistik terintegrasi.',
                'description' => 'Mengoperasikan armada kapal kargo kontainer, curah, dan tanker bersama terminal pelabuhan serta jaringan logistik darat dan gudang berikat.',
                'tags' => ['Container Shipping', 'Port Terminal', 'Bonded Warehouse', 'Supply Chain'],
            ],
            [
                'name' => 'PT Haldin Pacific Semesta',
                'slug' => 'pt-haldin-pacific-semesta',
                'industry_category' => 'Chemical & Ingredients',
                'city' => 'Bekasi',
                'founded_year' => 1989,
                'website' => 'https://haldin.com',
                'tagline' => 'Produsen bahan alami, ekstrak botani, dan bahan baku rasa.',
                'description' => 'Mengolah bahan baku pertanian Indonesia menjadi ekstrak teh, kopi, cokelat, gula kelapa, madu, serta minyak atsiri dan bahan fungsional industri.',
                'tags' => ['Natural Extracts', 'Botanical Ingredients', 'Essential Oils', 'Food Ingredients'],
            ],
            [
                'name' => 'PT Indesso Aroma',
                'slug' => 'pt-indesso-aroma',
                'industry_category' => 'Chemical & Ingredients',
                'city' => 'Jakarta',
                'founded_year' => 1968,
                'website' => 'https://www.indesso.com',
                'email' => 'contact@indesso.com',
                'tagline' => 'Produsen bahan aroma kimia, minyak atsiri, dan ekstrak alami.',
                'description' => 'Memproduksi bahan baku aroma, ekstrak rempah alami Indonesia, serta bahan perisa dan wewangian (flavor & fragrance ingredients) untuk industri pangan dan kosmetik.',
                'tags' => ['Aroma Chemicals', 'Essential Oils', 'Natural Extracts', 'Flavor & Fragrance'],
            ],
        ];
    }
}
