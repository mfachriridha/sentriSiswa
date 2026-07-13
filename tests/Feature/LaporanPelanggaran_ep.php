<?php

use App\Models\Kelas;
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
| Yang diuji di berkas ini adalah penyaringnya: rentang tanggal, kelas, tingkat, dan
| kategori. Membaca, mengunduh, dan mencetak laporannya ada di LaporanPelanggaran_uc.php,
| dan batas rentang tanggalnya di LaporanPelanggaran_bva.php.
|
*/

afterEach(function () {
    Carbon::setTestNow();
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
