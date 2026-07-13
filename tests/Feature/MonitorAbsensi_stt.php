<?php

use App\Models\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Kelas Saya (Wali Kelas) — State Transition Testing
|--------------------------------------------------------------------------
|
| Wali kelas ikut memindahkan keadaan absensi seorang siswa:
|
|   belum absen --(ditetapkan wali kelas)--> hadir / izin / sakit / alpha
|   hadir       --(dibetulkan wali kelas)--> izin  (misal siswanya menyusul surat izin)
|
| Jadi status yang sudah tercatat pun masih bisa ditimpa - itu memang gunanya:
| siswa yang terlanjur tercatat hadir bisa dibetulkan jadi izin, dan sebaliknya.
|
| Yang tidak bisa dipindahkan adalah keadaan di hari yang bukan hari absensi:
| jendelanya tidak pernah dibuka, jadi tidak ada yang bisa ditetapkan.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.MOA.003 / TC.MOA.003.001 — Positive — hadir → izin
test('wali kelas mengubah status absensi yang sudah tercatat sebelumnya', function () {
    Carbon::setTestNow('2026-07-06 07:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    // Siswa sudah absen sendiri pagi ini, lalu menyerahkan surat izin.
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');

    $this->followingRedirects()
        ->put("/wali-kelas/kelas-saya/{$siswa->nisn}/absensi", ['status' => 'izin'])
        ->assertSeeInOrder(['Status absensi hari ini berhasil diperbarui.', 'Ahmad Fauzi', 'Izin']);
});

// TS.MOA.004 / TC.MOA.004.001 — Negative — jendelanya tidak pernah dibuka
test('wali kelas gagal menetapkan status absensi di hari yang bukan hari absensi', function () {
    Carbon::setTestNow('2026-07-05 07:00:00'); // Minggu, bukan hari aktif absensi.
    [, , $siswa] = waliKelasDenganKelas();

    $this->followingRedirects()
        ->put("/wali-kelas/kelas-saya/{$siswa->nisn}/absensi", ['status' => 'hadir'])
        ->assertSee('Absensi hanya tersedia pada hari '.Pengaturan::labelHariAbsen().'.')
        ->assertDontSee('Status absensi hari ini berhasil diperbarui.');
});
