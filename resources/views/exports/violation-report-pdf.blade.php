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
            <h2>Ringkasan Sisa Poin Seluruh Siswa (diurutkan dari poin tersisa terkecil)</h2>
            <table>
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
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
                            <td>{{ $row['sisa_poin'] }}</td>
                            <td>{{ $row['sisa_poin'] <= 50 ? 'Perhatian' : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h2>Pelanggaran</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>Pelanggaran</th>
                <th>Kategori</th>
                <th>Poin</th>
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
                    <td>{{ $violation->dicatatOleh?->nama ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary" style="margin-top: 18px;">
        <h2>Penambahan Poin (Disetujui)</h2>
        <table>
            <thead>
                <tr>
                    <th>Tanggal Disetujui</th>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Alasan</th>
                    <th>Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengajuanPoin as $pengajuan)
                    <tr>
                        <td>{{ $pengajuan->disetujui_pada?->locale('id')->translatedFormat('d F Y') ?? '-' }}</td>
                        <td>{{ $pengajuan->profilSiswa?->pengguna?->nama ?? '-' }}</td>
                        <td>{{ $pengajuan->profilSiswa?->kelas?->nama ?? '-' }}</td>
                        <td>{{ $pengajuan->alasan }}</td>
                        <td>+{{ $pengajuan->jumlah_poin }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
