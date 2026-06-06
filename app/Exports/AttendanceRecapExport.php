<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

class AttendanceRecapExport implements FromArray, WithCharts, WithHeadings
{
    /**
     * @var list<array<int, int|string|float>>
     */
    private array $chartRows;

    /**
     * @param  list<array<int, int|string|float>>  $rows
     */
    public function __construct(private readonly array $rows)
    {
        $this->chartRows = [
            ['Hadir', collect($rows)->sum(fn (array $row): int => (int) ($row[2] ?? 0))],
            ['Terlambat', collect($rows)->sum(fn (array $row): int => (int) ($row[3] ?? 0))],
            ['Izin', collect($rows)->sum(fn (array $row): int => (int) ($row[4] ?? 0))],
            ['Sakit', collect($rows)->sum(fn (array $row): int => (int) ($row[5] ?? 0))],
            ['Alpha', collect($rows)->sum(fn (array $row): int => (int) ($row[6] ?? 0))],
        ];
    }

    /**
     * @return list<array<int, int|string|float>>
     */
    public function array(): array
    {
        return [
            ...$this->rows,
            [],
            ['Grafik Status Absensi'],
            ...$this->chartRows,
        ];
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

    public function charts(): Chart
    {
        $chartStartRow = count($this->rows) + 4;
        $chartEndRow = $chartStartRow + count($this->chartRows) - 1;

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            [0],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.($chartStartRow - 1), null, 1)],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "Worksheet!\$A\${$chartStartRow}:\$A\${$chartEndRow}", null, count($this->chartRows))],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "Worksheet!\$B\${$chartStartRow}:\$B\${$chartEndRow}", null, count($this->chartRows))],
        );
        $series->setPlotDirection(DataSeries::DIRECTION_BAR);

        $chart = new Chart(
            'grafik_absensi',
            new Title('Grafik Status Absensi'),
            new Legend(Legend::POSITION_RIGHT, null, false),
            new PlotArea(null, [$series]),
        );

        $chart->setTopLeftPosition('J2');
        $chart->setBottomRightPosition('Q18');

        return $chart;
    }
}
