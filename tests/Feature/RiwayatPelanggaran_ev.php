<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
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
|
*/

// TS.RIP.001 / TC.RIP.001.001 — Positive
test('wali kelas melihat riwayat pelanggaran siswa kelasnya', function () {
    [, , $siswa] = waliKelasDenganKelas();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06');

    $this->get('/wali-kelas/pelanggaran')
        ->assertSee('Riwayat Pelanggaran')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Terlambat masuk kelas')
        ->assertSee('Ringan');
});

// TS.RIP.002 / TC.RIP.002.001 — Negative
test('wali kelas tidak melihat pelanggaran siswa dari kelas lain', function () {
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

    catatPelanggaran($siswaLain, 'Membolos', 'sedang', '2026-07-06');

    $this->get('/wali-kelas/pelanggaran')
        ->assertDontSee('Siswa Kelas Lain')
        ->assertDontSee('Membolos');
});

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

// TS.RIP.006 / TC.RIP.006.001 — Negative
test('riwayat pelanggaran ditolak ketika tanggal selesai lebih awal daripada tanggal mulai', function () {
    waliKelasDenganKelas();

    $this->from('/wali-kelas/pelanggaran')
        ->followingRedirects()
        ->get('/wali-kelas/pelanggaran?date_from=2026-07-31&date_to=2026-07-01')
        ->assertSee('Tanggal selesai harus sama dengan atau setelah tanggal mulai.');
});

// TS.RIP.007 / TC.RIP.007.001 — Positive
test('kelas tanpa pelanggaran menampilkan keterangan riwayat masih kosong', function () {
    waliKelasDenganKelas();

    $this->get('/wali-kelas/pelanggaran')
        ->assertSee('Belum ada riwayat pelanggaran untuk kelas ini.');
});

// TS.RIP.008 / TC.RIP.008.001 — Negative
test('guru yang belum dipasangi kelas melihat keterangan belum ada kelas di riwayat pelanggaran', function () {
    $wali = Pengguna::factory()->homeroom()->create([
        'email' => 'wali.tanpa.kelas@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($wali);

    $this->get('/wali-kelas/pelanggaran')
        ->assertSee('Belum Ada Kelas')
        ->assertSee('Anda belum ditugaskan sebagai wali kelas.');
});
