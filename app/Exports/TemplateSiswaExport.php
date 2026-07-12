<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TemplateSiswaExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Nama',
            'NISN',
            'NIS',
            'L/P',
            'Kelas',
        ];
    }

    public function array(): array
    {
        return [
            ['Ahmad Fauzi', '0012345678', '12345', 'L', '10. 1'],
            ['Siti Nurhaliza', '0012345679', '12346', 'P', '10. 1'],
            ['Budi Prakoso', '0012345680', '12347', 'L', '11 IPA 1'],
        ];
    }
}
