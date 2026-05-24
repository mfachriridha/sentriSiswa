<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeacherTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Nama',
            'NIP',
            'Tipe',
            'Kelas',
        ];
    }

    public function array(): array
    {
        return [
            ['Siti Aminah, S.Pd', '197403052022211001', 'Wali Kelas', '10. 1'],
            ['Budi Santoso, M.Pd', '198506172022211002', 'Wali Kelas', '10. 2'],
            ['Ratna Dewi, S.Pd', '', 'BK', '11'],
        ];
    }
}
