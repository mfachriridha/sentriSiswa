<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilGuru;
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
| Rekap merangkum jumlah hadir, terlambat, izin, sakit, dan alpha tiap siswa pada
| rentang tanggal yang dipilih, beserta persentase kehadirannya. Rekap bisa
| disaring per kelas, per siswa, per status, atau per bulan, lalu diunduh sebagai
| berkas Excel maupun PDF.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.RAB.001 / TC.RAB.001.001 — Positive
test('guru bk melihat rekap kehadiran seluruh siswa di tingkatnya', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-07', 'terlambat');

    bkMasuk('10');

    $this->get('/bk/laporan?mulai=2026-07-06&selesai=2026-07-10')
        ->assertSee('Rekap Absensi Tingkat 10')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('100%');
});

// TS.RAB.002 / TC.RAB.002.001 — Negative
test('rekap bk tidak memuat siswa dari tingkat lain', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    kelasBerisiSiswa();

    $kelasTingkatLain = Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);
    siswaLainDiKelas($kelasTingkatLain->id, 'Siswa Tingkat 11', '1234567891', '10002');

    bkMasuk('10');

    $this->get('/bk/laporan?mulai=2026-07-06&selesai=2026-07-10')
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Siswa Tingkat 11');
});

// TS.RAB.003 / TC.RAB.003.001 — Positive
test('persentase kehadiran dihitung dari hari yang sudah punya keputusan', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-07', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-08', 'alpha');
    catatKehadiran($siswa->nisn, '2026-07-09', 'alpha');

    bkMasuk('10');

    $this->get('/bk/laporan?mulai=2026-07-06&selesai=2026-07-10')
        ->assertSee('50%');
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

// TS.RAB.007 / TC.RAB.007.001 — Positive
test('guru bk mengunduh rekap kehadiran dalam berkas excel', function () {
    Excel::fake();
    Carbon::setTestNow('2026-07-10 08:00:00');
    kelasBerisiSiswa();

    bkMasuk('10');

    $this->get('/bk/laporan/ekspor-excel?mulai=2026-07-01&selesai=2026-07-10')
        ->assertSuccessful();

    Excel::assertDownloaded('rekap-absensi-tingkat-10-2026-07-01-sampai-2026-07-10.xlsx');
});

// TS.RAB.008 / TC.RAB.008.001 — Positive
test('guru bk mengunduh rekap kehadiran dalam berkas pdf', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    kelasBerisiSiswa();

    bkMasuk('10');

    $this->get('/bk/laporan/ekspor-pdf?mulai=2026-07-01&selesai=2026-07-10')
        ->assertSuccessful()
        ->assertDownload('rekap-absensi-tingkat-10-2026-07-01-sampai-2026-07-10.pdf');
});

// TS.RAB.009 / TC.RAB.009.001 — Negative
test('guru bk yang belum dipasangi tingkat melihat keterangan datanya belum tersedia', function () {
    $bkTanpaTingkat = Pengguna::factory()->counselor()->create([
        'nama' => 'Ibu Sari',
        'email' => 'bk.tanpa.tingkat@sentrisiswa.test',
        'status' => 'registered',
    ]);

    ProfilGuru::factory()->counselor()->create([
        'pengguna_id' => $bkTanpaTingkat->id,
        'tingkat' => null,
    ]);

    masukSebagai($bkTanpaTingkat);

    $this->get('/bk/laporan')
        ->assertSee('Data Tingkat Tidak Tersedia');
});
