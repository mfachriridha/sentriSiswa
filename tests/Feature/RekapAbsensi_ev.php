<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Rekap Absensi (Wali Kelas) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: wali kelas masuk lewat halaman masuk, lalu membuka rekap
| kehadiran kelasnya. Hasilnya diperiksa dari apa yang muncul di layar, bukan
| dari basis data.
|
| Rekap merangkum jumlah hadir, terlambat, izin, sakit, dan alpha tiap siswa pada
| rentang tanggal yang dipilih, beserta persentase kehadirannya. Persentase
| dihitung dari hari yang sudah punya keputusan: (hadir + terlambat) dibagi
| seluruh hari yang tercatat. Rekap bisa disaring per siswa, per status, atau per
| bulan, dan bisa diunduh sebagai berkas Excel maupun PDF.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.REA.001 / TC.REA.001.001 — Positive
test('wali kelas melihat rekap kehadiran kelasnya', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-07', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-08', 'terlambat');

    $this->get('/wali-kelas/absensi?mulai=2026-07-06&selesai=2026-07-10')
        ->assertSee('Rekap Absensi')
        ->assertSee('10 IPA 1')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('100%');
});

// TS.REA.002 / TC.REA.002.001 — Positive
test('persentase kehadiran dihitung dari hari yang sudah punya keputusan', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    // Dua hari hadir dan dua hari alpha, sehingga kehadirannya separuh.
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-07', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-08', 'alpha');
    catatKehadiran($siswa->nisn, '2026-07-09', 'alpha');

    $this->get('/wali-kelas/absensi?mulai=2026-07-06&selesai=2026-07-10')
        ->assertSee('50%');
});

// TS.REA.003 / TC.REA.003.001 — Positive
test('kehadiran di hari yang bukan hari absensi tidak ikut dihitung', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');  // Senin, hari absensi.
    catatKehadiran($siswa->nisn, '2026-07-05', 'alpha');  // Minggu, bukan hari absensi.

    // Alpha di hari libur diabaikan, jadi kehadirannya tetap penuh.
    $this->get('/wali-kelas/absensi?mulai=2026-07-01&selesai=2026-07-10')
        ->assertSee('100%');
});

// TS.REA.004 / TC.REA.004.001 — Positive
test('wali kelas menyaring rekap hanya untuk seorang siswa', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, $kelas, $siswa] = waliKelasDenganKelas();
    siswaLainDiKelas($kelas->id, 'Siti Aminah', '1234567892', '10003');

    // Nama siswa lain tetap muncul sebagai pilihan di kolom penyaring, jadi yang
    // diperiksa adalah NIS-nya yang hanya tampil kalau siswa itu ada di tabel.
    $this->get("/wali-kelas/absensi?profil_siswa_id={$siswa->nisn}")
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10001')
        ->assertDontSee('10003');
});

// TS.REA.005 / TC.REA.005.001 — Positive
test('wali kelas menyaring rekap hanya siswa yang pernah alpha', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, $kelas, $siswa] = waliKelasDenganKelas();
    $siswaRajin = siswaLainDiKelas($kelas->id, 'Siswa Rajin', '1234567893', '10004');

    catatKehadiran($siswa->nisn, '2026-07-06', 'alpha');
    catatKehadiran($siswaRajin->nisn, '2026-07-06', 'hadir');

    $this->get('/wali-kelas/absensi?mulai=2026-07-01&selesai=2026-07-10&status=alpha')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10001')
        ->assertDontSee('10004');
});

// TS.REA.006 / TC.REA.006.001 — Positive
test('wali kelas menyaring rekap per bulan', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    catatKehadiran($siswa->nisn, '2026-06-01', 'alpha');  // Bulan lain.
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');  // Bulan yang dipilih.

    // Alpha di bulan Juni tidak ikut terhitung, jadi Juli tampil penuh.
    $this->get('/wali-kelas/absensi?month=2026-07')
        ->assertSee('100%');
});

/*
| Kalender tanggal selesai otomatis mengunci tanggal sebelum tanggal mulai, dan
| sebaliknya, sehingga pengguna tidak pernah bisa memilih rentang terbalik.
| Karena tidak pernah dialami pengguna, kasus tersebut tidak didokumentasikan.
*/

// TS.REA.007 / TC.REA.007.001 — Positive
test('wali kelas mengunduh rekap kehadiran dalam berkas excel', function () {
    Excel::fake();
    Carbon::setTestNow('2026-07-10 08:00:00');
    waliKelasDenganKelas();

    $this->get('/wali-kelas/absensi/ekspor-excel?mulai=2026-07-01&selesai=2026-07-10')
        ->assertSuccessful();

    Excel::assertDownloaded('rekap-absensi-10 IPA 1-2026-07-01-sampai-2026-07-10.xlsx');
});

// TS.REA.008 / TC.REA.008.001 — Positive
test('wali kelas membuka halaman cetak rekap kehadiran kelasnya', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');

    $this->get('/wali-kelas/absensi/cetak?mulai=2026-07-01&selesai=2026-07-10')
        ->assertSuccessful()
        ->assertSee('Rekap Absensi')
        ->assertSee('Kelas 10 IPA 1')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Cetak / Simpan PDF');
});

// TS.REA.009 / TC.REA.009.001 — Negative
test('guru yang belum dipasangi kelas melihat keterangan belum ada kelas', function () {
    $wali = Pengguna::factory()->homeroom()->create([
        'email' => 'wali.tanpa.kelas@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($wali);

    $this->get('/wali-kelas/absensi')
        ->assertSee('Belum Ada Kelas')
        ->assertSee('Anda belum ditugaskan sebagai wali kelas.');
});
