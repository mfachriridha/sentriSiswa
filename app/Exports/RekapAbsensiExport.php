<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

/**
 * WithStrictNullComparison wajib ada di sini. Tanpa itu, penulis Excel
 * membandingkan nilai sel dengan null secara longgar - dan di PHP 0 == null
 * bernilai benar, sehingga angka 0 (siswa yang izinnya nol, sakitnya nol)
 * ditulis sebagai sel kosong, bukan angka 0.
 */
class RekapAbsensiExport implements FromArray, WithHeadings, WithStrictNullComparison
{
    /**
     * @param  list<array<int, int|string|float>>  $rows
     */
    public function __construct(private readonly array $rows) {}

    /**
     * @return list<array<int, int|string|float>>
     */
    public function array(): array
    {
        return $this->rows;
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'NIS',
            'Nama',
            'Hadir',
            'Izin',
            'Sakit',
            'Alpha',
        ];
    }
}
