<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Nama',
            'NISN',
            'NIS',
            'Kelas',
        ];
    }

    public function array(): array
    {
        return [
            ['Ahmad Fauzi', '0012345678', '12345', '10. 1'],
            ['Siti Nurhaliza', '0012345679', '12346', '10. 1'],
            ['Budi Prakoso', '0012345680', '12347', '11 IPA 1'],
        ];
    }
}
