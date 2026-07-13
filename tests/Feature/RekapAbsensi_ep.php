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
| Rekap merangkum jumlah hadir, izin, sakit, dan alpha tiap siswa pada
| rentang tanggal yang dipilih, beserta persentase kehadirannya. Persentase
| dihitung dari hari yang sudah punya keputusan: hadir dibagi
| seluruh hari yang tercatat. Rekap bisa disaring per siswa, per status, atau per
| bulan, dan bisa diunduh sebagai berkas Excel maupun PDF.
| Yang diuji di berkas ini adalah penyaringnya: siswa yang dipilih, status kehadiran,
| dan bulan. Membaca, mengunduh, dan mencetak rekapnya ada di RekapAbsensi_uc.php,
| dan batas rentang tanggalnya di RekapAbsensi_bva.php.
|
*/

afterEach(function () {
    Carbon::setTestNow();
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
