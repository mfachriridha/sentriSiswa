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
        .flagged { background: #fef2f2; }
        h2 { font-size: 13px; margin: 0 0 8px; }
        .summary { margin-bottom: 18px; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>Dicetak {{ now()->locale('id')->translatedFormat('d F Y H:i') }}</p>

    @if (! empty($pointsSummary))
        <div class="summary">
            <h2>Ringkasan Poin Kritis (siswa dengan pelanggaran disetujui, diurutkan dari poin tersisa terkecil)</h2>
            <table>
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Total Poin Terpotong</th>
                        <th>Sisa Poin</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pointsSummary as $row)
                        <tr class="{{ $row['sisa_poin'] <= 50 ? 'flagged' : '' }}">
                            <td>{{ $row['nis'] }}</td>
                            <td>{{ $row['nama'] }}</td>
                            <td>{{ $row['kelas'] }}</td>
                            <td>-{{ $row['total_terpotong'] }}</td>
                            <td>{{ $row['sisa_poin'] }}</td>
                            <td>{{ $row['sisa_poin'] <= 50 ? 'Perhatian' : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

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
