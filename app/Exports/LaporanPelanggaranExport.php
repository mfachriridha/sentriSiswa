<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanPelanggaranExport implements WithMultipleSheets
{
    /**
     * @param  list<array<int, int|string|float>>  $violationRows
     * @param  list<array<int, int|string|float>>  $pointsSummaryRows
     * @param  list<array<int, int|string|float>>  $pointAdditionRows
     */
    public function __construct(
        private readonly array $violationRows,
        private readonly array $pointsSummaryRows,
        private readonly array $pointAdditionRows,
    ) {}

    /**
     * @return array<int, ArraySheetExport>
     */
    public function sheets(): array
    {
        return [
            new ArraySheetExport('Pelanggaran', [
                'Tanggal', 'NIS', 'Nama', 'Kelas', 'Pelanggaran', 'Kategori', 'Poin', 'Dicatat Oleh',
            ], $this->violationRows),
            new ArraySheetExport('Ringkasan Poin', [
                'NIS', 'Nama', 'Kelas', 'Sisa Poin',
            ], $this->pointsSummaryRows),
            new ArraySheetExport('Penambahan Poin', [
                'Tanggal Disetujui', 'NIS', 'Nama', 'Kelas', 'Alasan', 'Poin',
            ], $this->pointAdditionRows),
        ];
    }
}
