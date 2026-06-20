<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TemplateGuruExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Peran',
        ];
    }

    public function array(): array
    {
        return [
            ['Siti Aminah, S.Pd', 'siti.aminah@sekolah.id', 'Wali Kelas'],
            ['Budi Santoso, M.Pd', 'budi.santoso@sekolah.id', 'Wali Kelas'],
            ['Ratna Dewi, S.Pd', 'ratna.dewi@sekolah.id', 'BK'],
            ['Ahmad Hidayat, M.Pd', 'ahmad.hidayat@sekolah.id', 'Kesiswaan'],
        ];
    }
}
