<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Riwayat Pelanggaran (Wali Kelas) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: wali kelas masuk lewat halaman masuk, lalu membuka riwayat
| pelanggaran kelasnya. Hasilnya diperiksa dari apa yang muncul di layar, bukan
| dari basis data.
|
| Halaman ini hanya untuk melihat: wali kelas memantau pelanggaran yang sudah
| dicatat kesiswaan untuk siswa di kelasnya, dan bisa menyaringnya per siswa,
| per kategori, atau per rentang tanggal. Wali kelas tidak bisa menambah,
| mengubah, atau menghapus catatan pelanggaran dari sini.
| Yang diuji di berkas ini adalah penyaringnya: siswa, kategori, dan rentang tanggal.
|
*/

// TS.RIP.003 / TC.RIP.003.001 — Positive
test('wali kelas menyaring riwayat pelanggaran hanya untuk seorang siswa', function () {
    [, $kelas, $siswa] = waliKelasDenganKelas();
    $siswaLain = siswaLainDiKelas($kelas->id, 'Siti Aminah', '1234567892', '10003');

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06');
    catatPelanggaran($siswaLain, 'Membolos', 'sedang', '2026-07-06');

    $this->get("/wali-kelas/pelanggaran?profil_siswa_id={$siswa->nisn}")
        ->assertSee('Terlambat masuk kelas')
        ->assertDontSee('Membolos');
});

// TS.RIP.004 / TC.RIP.004.001 — Positive
test('wali kelas menyaring riwayat pelanggaran berdasarkan kategori', function () {
    [, , $siswa] = waliKelasDenganKelas();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06');
    catatPelanggaran($siswa, 'Berkelahi', 'berat', '2026-07-07', 25);

    $this->get('/wali-kelas/pelanggaran?kategori=berat')
        ->assertSee('Berkelahi')
        ->assertDontSee('Terlambat masuk kelas');
});

// TS.RIP.005 / TC.RIP.005.001 — Positive
test('wali kelas menyaring riwayat pelanggaran berdasarkan rentang tanggal', function () {
    [, , $siswa] = waliKelasDenganKelas();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-06-10');
    catatPelanggaran($siswa, 'Berkelahi', 'berat', '2026-07-07', 25);

    $this->get('/wali-kelas/pelanggaran?date_from=2026-07-01&date_to=2026-07-31')
        ->assertSee('Berkelahi')
        ->assertDontSee('Terlambat masuk kelas');
});

/*
| Kalender tanggal selesai otomatis mengunci tanggal sebelum tanggal mulai, dan
| sebaliknya, sehingga pengguna tidak pernah bisa memilih rentang terbalik.
| Karena tidak pernah dialami pengguna, kasus tersebut tidak didokumentasikan.
*/
