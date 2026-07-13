<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Pelanggaran Siswa (Kesiswaan) — State Transition Testing
|--------------------------------------------------------------------------
|
| Sisa poin seorang siswa berpindah keadaan mengikuti catatan pelanggarannya:
|
|   100 --(pelanggaran dicatat, potong 10)--> 90
|    90 --(catatan itu dihapus)------------> 100 lagi
|
| Poin tidak disimpan sebagai angka tersendiri: ia dihitung ulang tiap kali dibaca,
| dari catatan pelanggaran yang masih ada. Jadi menghapus sebuah catatan otomatis
| mengembalikan poin siswa seperti pelanggaran itu tidak pernah terjadi - tanpa
| ada yang mencatat siapa yang menghapusnya dan kenapa.
|
| Perilaku itu memang disengaja untuk membetulkan salah catat, tetapi selama ini
| tidak pernah dibuktikan. Kalau suatu hari poin berhenti pulih setelah penghapusan,
| tidak ada yang menangkapnya.
|
| Keadaan jenis pelanggarannya juga menentukan: yang sudah dinonaktifkan tidak bisa
| dipakai mencatat pelanggaran baru.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.PLS.002 / TC.PLS.002.001 — Positive — poin turun begitu pelanggaran dicatat
test('poin siswa berkurang sesuai jenis pelanggaran yang dipilih', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    $pelanggaran = catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    kesiswaanMasuk();

    // Poin siswa mulai dari 100, jadi setelah dipotong 10 sisanya 90.
    $this->get("/kesiswaan/pelanggaran-siswa/{$pelanggaran->id}")
        ->assertSee('10 poin')
        ->assertSee('Sisa Poin: 90');
});

// TS.PLS.004 / TC.PLS.004.001 — Negative — jenis nonaktif tidak bisa dipakai
test('pelanggaran ditolak ketika jenis pelanggarannya sudah dinonaktifkan', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    kesiswaanMasuk();
    $jenisNonaktif = jenisPelanggaranTersedia(['aktif' => false]);

    $this->from('/kesiswaan/pelanggaran-siswa/create')
        ->followingRedirects()
        ->post('/kesiswaan/pelanggaran-siswa', dataPelanggaranSiswa($siswa, $jenisNonaktif))
        ->assertSee('Jenis pelanggaran tidak aktif dan tidak dapat dipilih.');
});

// TS.PLS.010 / TC.PLS.010.001 — Positive — catatan dihapus, poin pulih
test('poin siswa kembali utuh setelah catatan pelanggarannya dihapus', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    $pelanggaran = catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    kesiswaanMasuk();

    // Sebelum dihapus: poinnya 90.
    $this->get("/kesiswaan/monitoring/{$siswa->nisn}")->assertSee('90');

    $this->followingRedirects()
        ->delete("/kesiswaan/pelanggaran-siswa/{$pelanggaran->id}")
        ->assertSee('Pelanggaran siswa berhasil dihapus.')
        ->assertSee('Belum ada catatan pelanggaran siswa.');

    // Sesudah dihapus: poinnya kembali 100, seolah pelanggaran itu tak pernah ada.
    $this->get("/kesiswaan/monitoring/{$siswa->nisn}")->assertSee('100');
});
