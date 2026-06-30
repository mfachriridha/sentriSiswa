<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        p { margin: 0 0 14px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 5px; vertical-align: top; }
        th { background: #f3f4f6; text-align: left; }
        .chart { margin: 12px 0 16px; }
        .bar-row { margin: 5px 0; }
        .bar-label { display: inline-block; width: 90px; }
        .bar-track { display: inline-block; width: 360px; height: 10px; background: #e5e7eb; vertical-align: middle; }
        .bar-fill { display: block; height: 10px; background: #14b8a6; }
        .bar-value { display: inline-block; width: 30px; text-align: right; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>Dicetak {{ now()->locale('id')->translatedFormat('d F Y H:i') }}</p>

    @php
        $maxChartValue = max(1, collect($chartRows)->max(fn (array $row): int => $row[1]) ?? 1);
    @endphp
    <div class="chart">
        <strong>Grafik Status Pelanggaran</strong>
        @foreach ($chartRows as $row)
            @php
                $width = max(4, ($row[1] / $maxChartValue) * 100);
            @endphp
            <div class="bar-row">
                <span class="bar-label">{{ $row[0] }}</span>
                <span class="bar-track"><span class="bar-fill" style="width: {{ $width }}%"></span></span>
                <span class="bar-value">{{ $row[1] }}</span>
            </div>
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>Pelanggaran</th>
                <th>Kategori</th>
                <th>Poin</th>
                <th>Status</th>
                <th>Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($violations as $violation)
                <tr>
                    <td>{{ $violation->tanggal_pelanggaran->locale('id')->translatedFormat('d F Y') }}</td>
                    <td>{{ $violation->profilSiswa?->pengguna?->nama ?? '-' }}</td>
                    <td>{{ $violation->profilSiswa?->kelas?->nama ?? '-' }}</td>
                    <td>{{ $violation->nama_pelanggaran }}</td>
                    <td>{{ $categoryLabels[$violation->kategori_pelanggaran] ?? $violation->kategori_pelanggaran }}</td>
                    <td>-{{ $violation->pengurangan_poin }}</td>
                    <td>{{ $statusLabels[$violation->status] ?? $violation->status }}</td>
                    <td>{{ $violation->dicatatOleh?->nama ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
