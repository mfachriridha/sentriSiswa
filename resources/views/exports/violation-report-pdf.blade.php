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
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>Dicetak {{ now()->translatedFormat('d F Y H:i') }}</p>

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
                    <td>{{ $violation->violation_date->format('Y-m-d') }}</td>
                    <td>{{ $violation->studentProfile?->user?->name ?? '-' }}</td>
                    <td>{{ $violation->studentProfile?->class?->name ?? '-' }}</td>
                    <td>{{ $violation->violation_name }}</td>
                    <td>{{ $categoryLabels[$violation->violation_category] ?? $violation->violation_category }}</td>
                    <td>{{ $violation->point_deduction }}</td>
                    <td>{{ $statusLabels[$violation->status] ?? $violation->status }}</td>
                    <td>{{ $violation->recordedBy?->name ?? '-' }}</td>
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
