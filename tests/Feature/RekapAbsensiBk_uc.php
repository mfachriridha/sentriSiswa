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
| Fitur Rekap Absensi (BK) — Use Case Testing
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
| Alur pemakaian tanpa isian: guru BK membaca rekap kehadiran seluruh siswa di
| tingkatnya, mengunduhnya jadi berkas Excel, dan membukanya di halaman cetak.
| Termasuk alur pengecualiannya: siswa dari tingkat lain tidak boleh ikut terbaca,
| dan guru BK yang belum dipasangi tingkat.
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
    catatKehadiran($siswa->nisn, '2026-07-07', 'hadir');

    bkMasuk('10');

    $respons = $this->get('/bk/laporan?mulai=2026-07-06&selesai=2026-07-10')
        ->assertSee('Rekap Absensi Tingkat 10')
        ->assertSee('Ahmad Fauzi');

    expect(rekapBarisSiswa($respons, 'Ahmad Fauzi'))
        ->toBe(['hadir' => 2, 'izin' => 0, 'sakit' => 0, 'alpha' => 0]);
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
test('tiap status kehadiran dihitung terpisah pada rekap', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-07', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-08', 'izin');
    catatKehadiran($siswa->nisn, '2026-07-09', 'alpha');

    bkMasuk('10');

    $respons = $this->get('/bk/laporan?mulai=2026-07-06&selesai=2026-07-10');

    expect(rekapBarisSiswa($respons, 'Ahmad Fauzi'))
        ->toBe(['hadir' => 2, 'izin' => 1, 'sakit' => 0, 'alpha' => 1]);
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

// TS.RAB.013 / TC.RAB.013.001 — Negative
test('ekspor rekap bk ditolak ketika penyaringnya tidak menemukan siswa satu pun', function () {
    Excel::fake();
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');

    bkMasuk('10');

    // Disaring "hanya yang pernah alpha", padahal tidak ada yang alpha.
    $this->followingRedirects()
        ->get('/bk/laporan/ekspor-excel?mulai=2026-07-01&selesai=2026-07-10&status=alpha')
        ->assertSee('Tidak ada siswa yang cocok dengan penyaring ini, jadi tidak ada yang bisa diekspor.');

    // Kalau berkasnya benar-benar terunduh, yang diterima peramban adalah berkas -
    // bukan halaman - dan pesan di atas tidak akan pernah muncul.
});

// TS.RAB.008 / TC.RAB.008.001 — Positive
test('halaman cetak rekap bk menyebutkan kelas tiap siswa', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    kelasBerisiSiswa();

    bkMasuk('10');

    // Satu tingkat berisi belasan kelas, jadi tanpa kolom Kelas tidak ada cara
    // membedakan siapa dari kelas mana.
    $this->get('/bk/laporan/cetak?mulai=2026-07-01&selesai=2026-07-10')
        ->assertSuccessful()
        ->assertSee('Rekap Absensi Tingkat 10')
        ->assertSee('Kelas')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10 IPA 1')
        ->assertSee('Cetak / Simpan PDF');
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
