<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Riwayat Pelanggaran (Wali Kelas) — Use Case Testing
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
| Alur pemakaian tanpa isian: wali kelas membaca riwayat pelanggaran kelasnya. Termasuk
| alur alternatifnya (kelasnya belum punya pelanggaran) dan pengecualiannya (pelanggaran
| kelas lain tidak terbaca, dan guru yang belum dipasangi kelas).
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

// TS.RIP.006 / TC.RIP.006.001 — Positive
test('kelas tanpa pelanggaran menampilkan keterangan riwayat masih kosong', function () {
    waliKelasDenganKelas();

    $this->get('/wali-kelas/pelanggaran')
        ->assertSee('Belum ada riwayat pelanggaran untuk kelas ini.');
});

// TS.RIP.007 / TC.RIP.007.001 — Negative
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
