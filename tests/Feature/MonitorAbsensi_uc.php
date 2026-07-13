<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Kelas Saya (Wali Kelas) — Use Case Testing
|--------------------------------------------------------------------------
|
| Alur pemakaiannya: wali kelas membuka kelasnya, membaca kehadiran hari ini, dan
| menelusuri rincian seorang siswa.
|
| Alur alternatif dan pengecualiannya: guru yang belum dipasangi kelas, dan wali
| kelas yang mencoba menyentuh siswa dari kelas lain.
|
| Ditambah alamat yang dipanggil halaman diam-diam untuk menyegarkan status absensi
| kelas tanpa memuat ulang halaman.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.MOA.001 / TC.MOA.001.001 — Positive
test('wali kelas melihat daftar siswa kelasnya beserta kehadiran hari ini', function () {
    Carbon::setTestNow('2026-07-06 07:00:00'); // Senin, hari aktif absensi.
    [, , $siswa] = waliKelasDenganKelas();
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');

    $this->get('/wali-kelas/kelas-saya')
        ->assertSee('Kelas Saya')
        ->assertSee('10 IPA 1')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Hadir');
});

// TS.MOA.005 / TC.MOA.005.001 — Negative — pengecualian: siswa dari kelas lain
test('wali kelas tidak bisa mengubah absensi siswa dari kelas lain', function () {
    Carbon::setTestNow('2026-07-06 07:00:00');
    waliKelasDenganKelas();

    $kelasLain = Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);
    $penggunaLain = Pengguna::factory()->student()->create([
        'nama' => 'Siswa Kelas Lain',
        'status' => 'registered',
    ]);
    $siswaLain = ProfilSiswa::factory()->create([
        'pengguna_id' => $penggunaLain->id,
        'nisn' => '1234567891',
        'nis' => '10002',
        'kelas_id' => $kelasLain->id,
    ]);

    $this->put("/wali-kelas/kelas-saya/{$siswaLain->nisn}/absensi", ['status' => 'hadir'])
        ->assertForbidden();
});

// TS.MOA.006 / TC.MOA.006.001 — Positive
test('wali kelas melihat rincian seorang siswa di kelasnya', function () {
    Carbon::setTestNow('2026-07-06 07:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    $this->get("/wali-kelas/kelas-saya/{$siswa->nisn}")
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Riwayat Kehadiran');
});

// TS.MOA.008 / TC.MOA.008.001 — Negative — alur alternatif: belum punya kelas
test('guru yang belum dipasangi kelas melihat keterangan bahwa ia belum punya kelas', function () {
    $wali = Pengguna::factory()->homeroom()->create([
        'email' => 'wali.tanpa.kelas@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($wali);

    $this->get('/wali-kelas/kelas-saya')
        ->assertSuccessful()
        ->assertDontSee('Ahmad Fauzi');
});

// TS.MOA.009 / TC.MOA.009.001 — Positive — penyegar status kelas
test('status absensi kelas ikut berubah setelah wali kelas menetapkannya', function () {
    Carbon::setTestNow('2026-07-06 07:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    // Halaman menanyakan status kelasnya secara berkala. Belum ada yang absen.
    $this->get('/wali-kelas/kelas-saya/status-absensi')
        ->assertSee('belum_absen');

    $this->put("/wali-kelas/kelas-saya/{$siswa->nisn}/absensi", ['status' => 'sakit']);

    $this->get('/wali-kelas/kelas-saya/status-absensi')
        ->assertSee('sakit');
});
