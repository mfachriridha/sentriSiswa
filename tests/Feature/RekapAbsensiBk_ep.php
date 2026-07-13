<?php

use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Rekap Absensi (BK) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: guru BK masuk lewat halaman masuk, lalu menyusun rekap
| kehadiran seluruh siswa di tingkat yang dipegangnya. Hasilnya diperiksa dari apa
| yang muncul di layar, bukan dari basis data.
|
| Rekap merangkum jumlah hadir, izin, sakit, dan alpha tiap siswa pada
| rentang tanggal yang dipilih, beserta persentase kehadirannya. Rekap bisa
| disaring per kelas, per siswa, per status, atau per bulan, lalu diunduh sebagai
| berkas Excel maupun PDF.
| Yang diuji di berkas ini adalah penyaringnya: kelas, status kehadiran, dan bulan.
| Membaca, mengunduh, dan mencetak rekapnya ada di RekapAbsensiBk_uc.php, dan batas
| rentang tanggalnya di RekapAbsensiBk_bva.php.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.RAB.004 / TC.RAB.004.001 — Positive
test('guru bk menyaring rekap berdasarkan kelas di tingkatnya', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, $kelas] = kelasBerisiSiswa();

    $kelasLain = Kelas::create(['nama' => '10 IPA 2', 'tingkat' => '10']);
    siswaLainDiKelas($kelasLain->id, 'Siswa Kelas Lain', '1234567891', '10002');

    bkMasuk('10');

    $this->get("/bk/laporan?kelas_id={$kelas->id}")
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('10002');
});

// TS.RAB.005 / TC.RAB.005.001 — Positive
test('guru bk menyaring rekap hanya siswa yang pernah alpha', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, $kelas, $siswa] = kelasBerisiSiswa();
    $siswaRajin = siswaLainDiKelas($kelas->id, 'Siswa Rajin', '1234567893', '10004');

    catatKehadiran($siswa->nisn, '2026-07-06', 'alpha');
    catatKehadiran($siswaRajin->nisn, '2026-07-06', 'hadir');

    bkMasuk('10');

    // Nama siswa juga muncul di kolom penyaring, jadi yang diperiksa NIS-nya.
    $this->get('/bk/laporan?mulai=2026-07-01&selesai=2026-07-10&status=alpha')
        ->assertSee('10001')
        ->assertDontSee('10004');
});

// TS.RAB.006 / TC.RAB.006.001 — Positive
test('guru bk menyaring rekap per bulan', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    catatKehadiran($siswa->nisn, '2026-06-01', 'alpha');
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');

    bkMasuk('10');

    // Alpha di bulan Juni tidak ikut terhitung, jadi Juli tampil penuh.
    $this->get('/bk/laporan?month=2026-07')
        ->assertSee('100%');
});
