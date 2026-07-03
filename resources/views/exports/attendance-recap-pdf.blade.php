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
        .flagged { background: #fef2f2; }
        .badge-flag { color: #b91c1c; font-weight: bold; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>Kelas {{ $className }} · {{ $startDate }} sampai {{ $endDate }}</p>

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
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $index => $student)
                @php
                    $stat = $stats[$student->nisn];
                    $flagged = $stat['alpha'] >= \App\Services\AbsenceWarningService::Threshold;
                @endphp
                <tr class="{{ $flagged ? 'flagged' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->nis ?? '-' }}</td>
                    <td>{{ $student->pengguna->nama }}</td>
                    <td class="center">{{ $stat['hadir'] }}</td>
                    <td class="center">{{ $stat['terlambat'] }}</td>
                    <td class="center">{{ $stat['izin'] }}</td>
                    <td class="center">{{ $stat['sakit'] }}</td>
                    <td class="center">{{ $stat['alpha'] }}</td>
                    <td class="center">{{ $stat['percentage'] }}%</td>
                    <td>{{ $flagged ? 'Perlu tindak lanjut' : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
