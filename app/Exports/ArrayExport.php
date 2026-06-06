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

class ArrayExport implements FromArray, WithCharts, WithHeadings
{
    /**
     * @param  list<string>  $headings
     * @param  list<array<int, int|string|float>>  $rows
     */
    public function __construct(
        private readonly array $headings,
        private readonly array $rows,
        private readonly array $chartRows = [],
        private readonly string $chartTitle = 'Grafik Laporan',
    ) {}

    /**
     * @return list<array<int, int|string|float>>
     */
    public function array(): array
    {
        if ($this->chartRows === []) {
            return $this->rows;
        }

        return [
            ...$this->rows,
            [],
            [$this->chartTitle],
            ...$this->chartRows,
        ];
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return $this->headings;
    }

    public function charts(): Chart|array
    {
        if ($this->chartRows === []) {
            return [];
        }

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
            'grafik_laporan',
            new Title($this->chartTitle),
            new Legend(Legend::POSITION_RIGHT, null, false),
            new PlotArea(null, [$series]),
        );

        $chart->setTopLeftPosition('K2');
        $chart->setBottomRightPosition('R18');

        return $chart;
    }
}
