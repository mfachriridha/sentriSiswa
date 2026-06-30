<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        p { margin: 0 0 14px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px; }
        th { background: #f3f4f6; text-align: left; }
        .center { text-align: center; }
        .chart { margin: 12px 0 16px; }
        .bar-row { margin: 5px 0; }
        .bar-label { display: inline-block; width: 80px; }
        .bar-track { display: inline-block; width: 360px; height: 10px; background: #e5e7eb; vertical-align: middle; }
        .bar-fill { display: block; height: 10px; background: #14b8a6; }
        .bar-value { display: inline-block; width: 30px; text-align: right; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>Kelas {{ $className }} · {{ $startDate }} sampai {{ $endDate }}</p>

    @php
        $maxChartValue = max(1, collect($chartRows)->max('value') ?? 1);
    @endphp
    <div class="chart">
        <strong>Grafik Status Absensi</strong>
        @foreach ($chartRows as $row)
            @php
                $width = max(4, ($row['value'] / $maxChartValue) * 100);
            @endphp
            <div class="bar-row">
                <span class="bar-label">{{ $row['label'] }}</span>
                <span class="bar-track"><span class="bar-fill" style="width: {{ $width }}%"></span></span>
                <span class="bar-value">{{ $row['value'] }}</span>
            </div>
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th class="center">Hadir</th>
                <th class="center">Terlambat</th>
                <th class="center">Izin</th>
                <th class="center">Sakit</th>
                <th class="center">Alpha</th>
                <th class="center">%</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $index => $student)
                @php
                    $stat = $stats[$student->id];
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->nis ?? '-' }}</td>
                    <td>{{ $student->pengguna->nama }}</td>
                    <td class="center">{{ $stat['hadir'] }}</td>
                    <td class="center">{{ $stat['terlambat'] }}</td>
                    <td class="center">{{ $stat['izin'] }}</td>
                    <td class="center">{{ $stat['sakit'] }}</td>
                    <td class="center">{{ $stat['alpha'] }}</td>
                    <td class="center">{{ $stat['percentage'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
