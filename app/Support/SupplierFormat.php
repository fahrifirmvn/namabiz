<?php

namespace App\Support;

class SupplierFormat
{
    public static function number(int|float $value, int $decimals = 0): string
    {
        return number_format($value, $decimals, ',', '.');
    }

    public static function capacity(string|int|float|null $value): string
    {
        $number = self::quantity($value);

        if ($number === null) {
            return self::text($value);
        }

        return $number >= 1_000_000
            ? rtrim(rtrim(self::number($number / 1_000_000, 2), '0'), ',').' juta unit/bln'
            : self::number($number).' unit/bln';
    }

    public static function moq(string|int|float|null $value): string
    {
        $number = self::quantity($value);

        return $number === null ? self::text($value) : self::number($number).' unit';
    }

    public static function leadTime(?string $value): string
    {
        return self::text($value);
    }

    public static function text(string|int|float|null $value): string
    {
        if ($value === null || trim((string) $value) === '') {
            return 'Belum tersedia';
        }

        $text = str_ireplace(
            ['units/mo', 'units/month', 'units', 'weeks', 'week', 'employees', 'Available', 'Partial'],
            ['unit/bln', 'unit/bln', 'unit', 'minggu', 'minggu', 'karyawan', 'Tersedia', 'Sebagian'],
            (string) $value,
        );

        return preg_replace('/(?<=\d)\s*-\s*(?=\d)/', '–', $text) ?? $text;
    }

    public static function category(string $value): string
    {
        return [
            'Distributor & Converter' => 'Distributor & Konverter',
            'Contract Manufacturer' => 'Produsen Kontrak',
            'Raw Material Supplier' => 'Pemasok Bahan Baku',
            'Packaging Manufacturer' => 'Produsen Kemasan',
            'Chemical & Ingredients' => 'Bahan Kimia & Ingredien',
            'Logistics & Warehousing' => 'Logistik & Pergudangan',
            'Spices & Coconut' => 'Rempah & Kelapa',
        ][$value] ?? $value;
    }

    public static function categoryIcon(string $value): string
    {
        return [
            'Distributor & Converter' => 'truck',
            'Contract Manufacturer' => 'cog-6-tooth',
            'Raw Material Supplier' => 'beaker',
            'Packaging Manufacturer' => 'archive-box',
            'Chemical & Ingredients' => 'funnel',
            'Logistics & Warehousing' => 'building-office-2',
            'Spices & Coconut' => 'globe-asia-australia',
        ][$value] ?? 'squares-2x2';
    }

    public static function tag(string $value): string
    {
        return [
            'Corrugated Boxes' => 'Kardus Bergelombang', 'Custom Structural Design' => 'Desain Struktur Khusus',
            'Offset Printing' => 'Cetak Ofset', 'Raw Resins' => 'Resin Bahan Baku',
            'Steel Coils' => 'Gulungan Baja', 'Glass Bottles' => 'Botol Kaca',
            'Aluminum Cans' => 'Kaleng Aluminium', 'Labels & Sleeves' => 'Label & Selongsong',
            'PET Bottles' => 'Botol PET', 'Food Containers' => 'Wadah Makanan',
            'Custom Molding' => 'Cetakan Khusus', 'Flexible Film' => 'Film Fleksibel',
            'Shrink Wrap' => 'Plastik Pembungkus', 'Injection Molding' => 'Cetak Injeksi',
            'CNC Machining' => 'Pemesinan CNC', 'Metal Casting' => 'Pengecoran Logam',
            'Forging' => 'Penempaan', 'PCB Assembly' => 'Perakitan PCB',
            'Electronics Manufacturing' => 'Produksi Elektronik',
        ][$value] ?? $value;
    }

    public static function quantity(string|int|float|null $value): ?float
    {
        if ($value === null || ! preg_match('/^\s*([\d.,]+)\s*(juta|ribu|jt|m|k)?\s*(?:units?|\/|\s|bln|mo|month)*$/i', (string) $value, $matches)) {
            return null;
        }

        $raw = $matches[1];
        $number = preg_match('/^\d{1,3}([.,]\d{3})+$/', $raw)
            ? (float) str_replace([',', '.'], '', $raw)
            : (float) str_replace(',', '.', $raw);
        $multiplier = match (strtolower($matches[2] ?? '')) {
            'm', 'jt', 'juta' => 1_000_000,
            'k', 'ribu' => 1_000,
            default => 1,
        };

        return $number * $multiplier;
    }
}
