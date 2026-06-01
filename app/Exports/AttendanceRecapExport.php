<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceRecapExport implements FromArray, WithHeadings
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
            'Terlambat',
            'Izin',
            'Sakit',
            'Alpha',
            'Persentase Kehadiran',
        ];
    }
}
