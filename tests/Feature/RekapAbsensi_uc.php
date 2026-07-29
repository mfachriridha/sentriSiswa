<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Rekap Absensi (Wali Kelas) — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: wali kelas masuk lewat halaman masuk, lalu membuka rekap
| kehadiran kelasnya. Hasilnya diperiksa dari apa yang muncul di layar, bukan
| dari basis data.
|
| Rekap merangkum jumlah hadir, izin, sakit, dan alpha tiap siswa pada
| rentang tanggal yang dipilih, beserta persentase kehadirannya. Persentase
| dihitung dari hari yang sudah punya keputusan: hadir dibagi
| seluruh hari yang tercatat. Rekap bisa disaring per siswa, per status, atau per
| bulan, dan bisa diunduh sebagai berkas Excel maupun PDF.
| Alur pemakaian tanpa isian: wali kelas membaca rekap kehadiran kelasnya, mengunduhnya
| jadi berkas Excel, dan membukanya di halaman cetak. Termasuk alur pengecualiannya:
| guru yang belum dipasangi kelas.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.REA.001 / TC.REA.001.001 — Positive
test('wali kelas melihat rekap kehadiran kelasnya', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-07', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-08', 'hadir');

    $this->get('/wali-kelas/absensi?mulai=2026-07-06&selesai=2026-07-10')
        ->assertSee('Rekap Absensi')
        ->assertSee('10 IPA 1')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('100%');
});

// TS.REA.002 / TC.REA.002.001 — Positive
test('persentase kehadiran dihitung dari hari yang sudah punya keputusan', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    // Dua hari hadir dan dua hari alpha, sehingga kehadirannya separuh.
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-07', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-08', 'alpha');
    catatKehadiran($siswa->nisn, '2026-07-09', 'alpha');

    $this->get('/wali-kelas/absensi?mulai=2026-07-06&selesai=2026-07-10')
        ->assertSee('50%');
});

// TS.REA.003 / TC.REA.003.001 — Positive
test('rekap menghitung baris absensi apa adanya, tidak bergantung pengaturan hari aktif saat ini', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');  // Senin, hari absensi.
    catatKehadiran($siswa->nisn, '2026-07-05', 'alpha');  // Minggu, di luar hari aktif baku.

    // Baris yang sudah tercatat di DB dihitung apa adanya - bukan disaring ulang
    // berdasar pengaturan hari aktif SAAT laporan dibuka. Kalau tidak, mengubah
    // pengaturan itu bisa mengubah rekap tanggal-tanggal lampau secara retroaktif.
    $this->get('/wali-kelas/absensi?mulai=2026-07-01&selesai=2026-07-10')
        ->assertSee('50%');
});

// TS.REA.007 / TC.REA.007.001 — Positive
test('wali kelas mengunduh rekap kehadiran dalam berkas excel', function () {
    Excel::fake();
    Carbon::setTestNow('2026-07-10 08:00:00');
    waliKelasDenganKelas();

    $this->get('/wali-kelas/absensi/ekspor-excel?mulai=2026-07-01&selesai=2026-07-10')
        ->assertSuccessful();

    Excel::assertDownloaded('rekap-absensi-10 IPA 1-2026-07-01-sampai-2026-07-10.xlsx');
});

// TS.REA.013 / TC.REA.013.001 — Negative
test('ekspor ditolak ketika penyaringnya tidak menemukan siswa satu pun', function () {
    Excel::fake();
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');

    // Disaring "hanya yang pernah alpha", padahal tidak ada yang alpha. Tanpa
    // penjagaan ini, berkasnya tetap terunduh - cuma berisi judul kolom - dan wali
    // kelas mengira ekspornya berhasil.
    $this->followingRedirects()
        ->get('/wali-kelas/absensi/ekspor-excel?mulai=2026-07-01&selesai=2026-07-10&status=alpha')
        ->assertSee('Tidak ada siswa yang cocok dengan penyaring ini, jadi tidak ada yang bisa diekspor.');

    // Kalau berkasnya benar-benar terunduh, yang diterima peramban adalah berkas -
    // bukan halaman - dan pesan di atas tidak akan pernah muncul.
});

// TS.REA.008 / TC.REA.008.001 — Positive
test('wali kelas membuka halaman cetak rekap kehadiran kelasnya', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');

    $this->get('/wali-kelas/absensi/cetak?mulai=2026-07-01&selesai=2026-07-10')
        ->assertSuccessful()
        ->assertSee('Rekap Absensi')
        ->assertSee('Kelas 10 IPA 1')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Cetak / Simpan PDF');
});

// TS.REA.009 / TC.REA.009.001 — Negative
test('guru yang belum dipasangi kelas melihat keterangan belum ada kelas', function () {
    $wali = Pengguna::factory()->homeroom()->create([
        'email' => 'wali.tanpa.kelas@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($wali);

    $this->get('/wali-kelas/absensi')
        ->assertSee('Belum Ada Kelas')
        ->assertSee('Anda belum ditugaskan sebagai wali kelas.');
});
