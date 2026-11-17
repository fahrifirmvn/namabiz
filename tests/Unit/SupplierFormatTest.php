<?php

namespace Tests\Unit;

use App\Support\SupplierFormat;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class SupplierFormatTest extends TestCase
{
    #[TestWith([2500000, '2,5 juta unit/bln'])]
    #[TestWith(['2.5M units/mo', '2,5 juta unit/bln'])]
    #[TestWith(['2,5 Jt / bln', '2,5 juta unit/bln'])]
    #[TestWith(['800K units/mo', '800.000 unit/bln'])]
    #[TestWith(['1M units/mo', '1 juta unit/bln'])]
    #[TestWith([null, 'Belum tersedia'])]
    public function test_capacity_uses_indonesian_numbers(string|int|null $input, string $expected): void
    {
        $this->assertSame($expected, SupplierFormat::capacity($input));
    }

    #[TestWith(['10,000 units', '10.000 unit'])]
    #[TestWith(['18.707 unit', '18.707 unit'])]
    #[TestWith([10000, '10.000 unit'])]
    #[TestWith([null, 'Belum tersedia'])]
    #[TestWith(['Sesuai pesanan', 'Sesuai pesanan'])]
    public function test_moq_does_not_invent_missing_quantities(string|int|null $input, string $expected): void
    {
        $this->assertSame($expected, SupplierFormat::moq($input));
    }

    public function test_lead_time_is_translated_without_inventing_a_fallback(): void
    {
        $this->assertSame('3–10 minggu', SupplierFormat::leadTime('3-10 weeks'));
        $this->assertSame('Belum tersedia', SupplierFormat::leadTime(null));
    }
}
