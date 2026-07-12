<?php

use App\Models\Kelas;
use App\Models\PengajuanPoin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Laporan Kesiswaan — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: kesiswaan masuk lewat halaman masuk, lalu menyusun laporan
| pelanggaran seluruh sekolah. Hasilnya diperiksa dari apa yang muncul di layar,
| bukan dari basis data.
|
| Laporan memuat dua tabel: pelanggaran yang memotong poin, dan penambahan poin
| yang sudah disetujui. Keduanya bisa disaring per rentang tanggal, per kelas,
| per tingkat, dan per kategori, lalu diunduh sebagai berkas Excel maupun PDF.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

/** Sebuah pengajuan poin yang sudah disetujui pada tanggal tertentu. */
function pengajuanPoinDisetujui(string $nisn, int $waliId, string $alasan, int $jumlahPoin, string $tanggalPersetujuan): PengajuanPoin
{
    return PengajuanPoin::create([
        'profil_siswa_id' => $nisn,
        'diajukan_oleh_id' => $waliId,
        'alasan' => $alasan,
        'status' => 'approved',
        'jumlah_poin' => $jumlahPoin,
        'disetujui_pada' => $tanggalPersetujuan,
    ]);
}

// TS.LAP.001 / TC.LAP.001.001 — Positive
test('kesiswaan melihat laporan pelanggaran seluruh sekolah', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan')
        ->assertSee('Laporan Kesiswaan')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10 IPA 1')
        ->assertSee('Terlambat masuk kelas')
        ->assertSee('-10');
});

// TS.LAP.002 / TC.LAP.002.001 — Positive
test('laporan memuat penambahan poin yang sudah disetujui', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    pengajuanPoinDisetujui($siswa->nisn, $wali->id, 'Juara lomba cerdas cermat.', 5, '2026-07-06');

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan')
        ->assertSee('Penambahan Poin (Disetujui)')
        ->assertSee('Juara lomba cerdas cermat.')
        ->assertSee('+5');
});

// TS.LAP.003 / TC.LAP.003.001 — Positive
test('kesiswaan menyaring laporan berdasarkan rentang tanggal', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-06-10', 10);
    catatPelanggaran($siswa, 'Berkelahi', 'berat', '2026-07-07', 60);

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan?mulai=2026-07-01&selesai=2026-07-31')
        ->assertSee('Berkelahi')
        ->assertDontSee('Terlambat masuk kelas');
});

// TS.LAP.004 / TC.LAP.004.001 — Positive
test('kesiswaan menyaring laporan berdasarkan kelas', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    $kelasLain = Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);
    $siswaLain = siswaLainDiKelas($kelasLain->id, 'Siswa Kelas Lain', '1234567891', '10002');
    catatPelanggaran($siswaLain, 'Berkelahi', 'berat', '2026-07-06', 60);

    kesiswaanMasuk();

    $this->get("/kesiswaan/laporan?kelas_id={$kelasLain->id}")
        ->assertSee('Siswa Kelas Lain')
        ->assertDontSee('Ahmad Fauzi');
});

// TS.LAP.005 / TC.LAP.005.001 — Positive
test('kesiswaan menyaring laporan berdasarkan tingkat', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    $kelasLain = Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);
    $siswaLain = siswaLainDiKelas($kelasLain->id, 'Siswa Kelas Lain', '1234567891', '10002');
    catatPelanggaran($siswaLain, 'Berkelahi', 'berat', '2026-07-06', 60);

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan?tingkat=11')
        ->assertSee('Siswa Kelas Lain')
        ->assertDontSee('Ahmad Fauzi');
});

// TS.LAP.006 / TC.LAP.006.001 — Positive
test('kesiswaan menyaring laporan berdasarkan kategori', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);
    catatPelanggaran($siswa, 'Berkelahi', 'berat', '2026-07-07', 60);

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan?kategori=berat')
        ->assertSee('Berkelahi')
        ->assertDontSee('Terlambat masuk kelas');
});

/*
| Kalender tanggal selesai otomatis mengunci tanggal sebelum tanggal mulai, dan
| sebaliknya, sehingga pengguna tidak pernah bisa memilih rentang terbalik.
| Karena tidak pernah dialami pengguna, kasus tersebut tidak didokumentasikan.
*/

// TS.LAP.007 / TC.LAP.007.001 — Positive
test('kesiswaan mengunduh laporan dalam berkas excel', function () {
    Excel::fake();
    kelasBerisiSiswa();
    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan/ekspor-excel')
        ->assertSuccessful();

    Excel::assertDownloaded('laporan-pelanggaran.xlsx');
});

// TS.LAP.008 / TC.LAP.008.001 — Positive
test('kesiswaan mengunduh laporan dalam berkas pdf', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan/ekspor-pdf')
        ->assertSuccessful()
        ->assertDownload('laporan-pelanggaran.pdf');
});

// TS.LAP.009 / TC.LAP.009.001 — Positive
test('laporan yang tidak menemukan data menampilkan keterangannya', function () {
    kelasBerisiSiswa();
    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan')
        ->assertSee('Tidak ada data laporan.')
        ->assertSee('Tidak ada penambahan poin disetujui pada rentang ini.');
});
