<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Pengajuan Poin (Wali Kelas) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Batas yang diuji adalah panjang alasan pengajuan: minimal 1 karakter dan
| maksimal 1000 karakter. Diuji tepat di bawah, tepat pada, dan tepat di atas
| kedua batas itu.
|
*/

// TS.PPW.010 / TC.PPW.010.001 — Negative — batas bawah, 0 karakter
test('alasan kosong ditolak', function () {
    [, , $siswa] = waliKelasDenganKelas();

    $this->from('/wali-kelas/pengajuan-poin/buat')
        ->followingRedirects()
        ->post('/wali-kelas/pengajuan-poin', pengajuanPoinSah($siswa, ['alasan' => '']))
        ->assertSee('Alasan wajib diisi.');
});

// TS.PPW.010 / TC.PPW.010.002 — Positive — tepat di batas bawah, 1 karakter
test('alasan sepanjang satu karakter diterima', function () {
    [, , $siswa] = waliKelasDenganKelas();

    $this->followingRedirects()
        ->post('/wali-kelas/pengajuan-poin', pengajuanPoinSah($siswa, ['alasan' => 'A']))
        ->assertSee('Pengajuan penambahan poin berhasil dikirim ke kesiswaan.');
});

// TS.PPW.011 / TC.PPW.011.001 — Positive — tepat di bawah batas atas, 999 karakter
test('alasan sepanjang 999 karakter diterima', function () {
    [, , $siswa] = waliKelasDenganKelas();

    $this->followingRedirects()
        ->post('/wali-kelas/pengajuan-poin', pengajuanPoinSah($siswa, ['alasan' => str_repeat('a', 999)]))
        ->assertSee('Pengajuan penambahan poin berhasil dikirim ke kesiswaan.');
});

// TS.PPW.011 / TC.PPW.011.002 — Positive — tepat di batas atas, 1000 karakter
test('alasan sepanjang 1000 karakter diterima', function () {
    [, , $siswa] = waliKelasDenganKelas();

    $this->followingRedirects()
        ->post('/wali-kelas/pengajuan-poin', pengajuanPoinSah($siswa, ['alasan' => str_repeat('a', 1000)]))
        ->assertSee('Pengajuan penambahan poin berhasil dikirim ke kesiswaan.');
});

// TS.PPW.011 / TC.PPW.011.003 — Negative — sekarakter di atas batas atas, 1001 karakter
test('alasan sepanjang 1001 karakter ditolak', function () {
    [, , $siswa] = waliKelasDenganKelas();

    $this->from('/wali-kelas/pengajuan-poin/buat')
        ->followingRedirects()
        ->post('/wali-kelas/pengajuan-poin', pengajuanPoinSah($siswa, ['alasan' => str_repeat('a', 1001)]))
        ->assertSee('Alasan maksimal 1000 karakter.');
});
