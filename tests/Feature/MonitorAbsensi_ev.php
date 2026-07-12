<?php

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Pengaturan;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Kelas Saya (Wali Kelas) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: wali kelas masuk lewat halaman masuk, lalu memantau
| kehadiran kelasnya seperti pengguna biasa. Hasilnya diperiksa dari apa yang
| muncul di layar, bukan dari basis data.
|
| Wali kelas melihat kehadiran hari ini untuk kelas yang dipegangnya, dan bisa
| menetapkan status absensi seorang siswa secara manual ke salah satu dari empat
| pilihan: Hadir, Izin, Sakit, atau Alpha. Ini dipakai misalnya ketika siswa
| menyerahkan surat izin atau surat sakit, termasuk untuk membetulkan status yang
| terlanjur tercatat.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.MOA.001 / TC.MOA.001.001 — Positive
test('wali kelas melihat daftar siswa kelasnya beserta kehadiran hari ini', function () {
    Carbon::setTestNow('2026-07-06 07:00:00'); // Senin, hari aktif absensi.
    [, , $siswa] = waliKelasDenganKelas();

    Absensi::create([
        'profil_siswa_id' => $siswa->nisn,
        'tanggal' => today()->toDateString(),
        'status' => 'hadir',
        'waktu_masuk' => '06:45:00',
    ]);

    $this->get('/wali-kelas/kelas-saya')
        ->assertSee('Kelas Saya')
        ->assertSee('10 IPA 1')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Hadir');
});

// TS.MOA.002 / TC.MOA.002.001 sampai TC.MOA.002.004 — Positive
test('wali kelas menetapkan status absensi siswa secara manual', function (string $status, string $label) {
    Carbon::setTestNow('2026-07-06 07:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    $this->followingRedirects()
        ->put("/wali-kelas/kelas-saya/{$siswa->nisn}/absensi", ['status' => $status])
        // Label statusnya juga dipakai kartu ringkasan dan kolom penyaring di
        // halaman yang sama, jadi yang diperiksa urutannya: label itu harus muncul
        // sesudah nama siswanya, yaitu di barisnya sendiri.
        ->assertSeeInOrder(['Status absensi hari ini berhasil diperbarui.', 'Ahmad Fauzi', $label]);
})->with([
    'hadir' => ['hadir', 'Hadir'],
    'izin' => ['izin', 'Izin'],
    'sakit' => ['sakit', 'Sakit'],
    'alpha' => ['alpha', 'Alpha'],
]);

// TS.MOA.003 / TC.MOA.003.001 — Positive
test('wali kelas mengubah status absensi yang sudah tercatat sebelumnya', function () {
    Carbon::setTestNow('2026-07-06 07:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    // Siswa sudah absen sendiri pagi ini, lalu menyerahkan surat izin.
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');

    $this->followingRedirects()
        ->put("/wali-kelas/kelas-saya/{$siswa->nisn}/absensi", ['status' => 'izin'])
        ->assertSeeInOrder(['Status absensi hari ini berhasil diperbarui.', 'Ahmad Fauzi', 'Izin']);
});

// TS.MOA.004 / TC.MOA.004.001 — Negative
test('wali kelas gagal menetapkan status absensi di hari yang bukan hari absensi', function () {
    Carbon::setTestNow('2026-07-05 07:00:00'); // Minggu, bukan hari aktif absensi.
    [, , $siswa] = waliKelasDenganKelas();

    $this->followingRedirects()
        ->put("/wali-kelas/kelas-saya/{$siswa->nisn}/absensi", ['status' => 'hadir'])
        ->assertSee('Absensi hanya tersedia pada hari '.Pengaturan::labelHariAbsen().'.')
        ->assertDontSee('Status absensi hari ini berhasil diperbarui.');
});

/*
| Status absensi hanya bisa dipilih dari tombol yang tersedia di layar, sehingga
| pengguna tidak mungkin mengirim status di luar pilihan itu. Karena tidak pernah
| dialami pengguna, kasus tersebut tidak didokumentasikan.
*/

// TS.MOA.005 / TC.MOA.005.001 — Negative
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

// TS.MOA.007 / TC.MOA.007.001 — Positive
test('wali kelas mencari siswa di kelasnya berdasarkan nama', function () {
    Carbon::setTestNow('2026-07-06 07:00:00');
    [, $kelas] = waliKelasDenganKelas();

    $penggunaLain = Pengguna::factory()->student()->create([
        'nama' => 'Siti Aminah',
        'status' => 'registered',
    ]);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $penggunaLain->id,
        'nisn' => '1234567892',
        'nis' => '10003',
        'kelas_id' => $kelas->id,
    ]);

    $this->get('/wali-kelas/kelas-saya?search=Siti')
        ->assertSee('Siti Aminah')
        ->assertDontSee('Ahmad Fauzi');
});

// TS.MOA.008 / TC.MOA.008.001 — Negative
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
