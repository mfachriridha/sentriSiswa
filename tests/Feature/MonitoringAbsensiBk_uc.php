<?php

use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Monitoring Absensi (BK) — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: guru BK masuk lewat halaman masuk, lalu memantau kehadiran
| siswa. Hasilnya diperiksa dari apa yang muncul di layar, bukan dari basis data.
|
| Tiap guru BK hanya memegang satu tingkat, dan hanya boleh memantau siswa di
| tingkat itu. Siswa dari tingkat lain tidak tampil dan rinciannya pun tidak bisa
| dibuka.
| Alur pemakaian tanpa isian: guru BK memantau kehadiran siswa di tingkatnya dan
| menelusuri riwayat seorang siswa. Termasuk alur pengecualiannya: siswa dari tingkat
| lain tidak boleh terbaca maupun dibuka rinciannya.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.MAB.001 / TC.MAB.001.001 — Positive
test('guru bk memantau kehadiran hari ini siswa di tingkatnya', function () {
    Carbon::setTestNow('2026-07-06 08:00:00'); // Senin, hari absensi.
    [, , $siswa] = kelasBerisiSiswa();
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');

    bkMasuk('10');

    $this->get('/bk/monitoring')
        ->assertSee('Monitoring BK')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10 IPA 1')
        ->assertSee('Hadir');
});

// TS.MAB.002 / TC.MAB.002.001 — Negative
test('guru bk tidak memantau siswa dari tingkat lain', function () {
    kelasBerisiSiswa();

    $kelasTingkatLain = Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);
    siswaLainDiKelas($kelasTingkatLain->id, 'Siswa Tingkat 11', '1234567891', '10002');

    bkMasuk('10');

    $this->get('/bk/monitoring')
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Siswa Tingkat 11');
});

// TS.MAB.003 / TC.MAB.003.001 — Negative
test('guru bk tidak bisa membuka rincian siswa dari tingkat lain', function () {
    kelasBerisiSiswa();

    $kelasTingkatLain = Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);
    $siswaTingkatLain = siswaLainDiKelas($kelasTingkatLain->id, 'Siswa Tingkat 11', '1234567891', '10002');

    bkMasuk('10');

    $this->get("/bk/monitoring/{$siswaTingkatLain->nisn}")
        ->assertForbidden();
});

// TS.MAB.004 / TC.MAB.004.001 — Positive
test('guru bk melihat riwayat kehadiran seorang siswa di tingkatnya', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    catatKehadiran($siswa->nisn, '2026-07-06', 'alpha');

    bkMasuk('10');

    $this->get("/bk/monitoring/{$siswa->nisn}")
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Riwayat Kehadiran')
        ->assertSee('Alpha');
});
