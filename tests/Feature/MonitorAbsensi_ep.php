<?php

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
| Dua isian yang diuji di berkas ini:
|   - Status absensi yang ditetapkan wali kelas. Himpunannya empat: Hadir, Izin,
|     Sakit, Alpha. Keempatnya diuji, karena tiap anggota himpunan itu memang
|     dipakai di sekolah dan salah satunya saja tidak mewakili yang lain.
|   - Kata kunci pencarian siswa: ketemu atau tidak ketemu.
|
| Menimpa status yang sudah tercatat ada di MonitorAbsensi_stt.php, dan alur
| membaca kelas beserta pengecualiannya di MonitorAbsensi_uc.php.
|
| Status absensi hanya bisa dipilih dari tombol yang tersedia di layar, sehingga
| pengguna tidak mungkin mengirim status di luar keempat pilihan itu. Karena tidak
| pernah dialami pengguna, kasus tersebut tidak didokumentasikan.
|
*/

afterEach(function () {
    Carbon::setTestNow();
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
