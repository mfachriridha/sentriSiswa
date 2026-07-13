<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Dashboard — Use Case Testing
|--------------------------------------------------------------------------
|
| Halaman pertama yang dilihat tiap orang setelah masuk. Tidak ada isian apa pun
| di sini: dashboard hanya merangkum keadaan hari ini untuk peran yang membukanya.
|
|   Admin      : jumlah siswa, guru, dan kelas yang benar-benar terdaftar.
|   Wali kelas : kelasnya, dan kehadiran kelasnya hari ini.
|   Guru BK    : tingkat yang dipegangnya, beserta jumlah kelas dan siswanya.
|   Kesiswaan  : jumlah kelas, siswa, pelanggaran, dan pengajuan poin yang menunggu.
|   Siswa      : sisa poinnya sendiri dan rekap kehadirannya.
|
| Angka-angka inilah yang jadi dasar orang mengambil tindakan, dan sampai sekarang
| tidak pernah satu kali pun diperiksa - kartu dashboard guru bahkan sempat diubah
| tanpa satu pun pengujian gagal.
|
| Termasuk alur alternatifnya: wali kelas yang belum dipasangi kelas.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.DSB.001 / TC.DSB.001.001 — Positive
test('admin melihat jumlah siswa, guru, dan kelas yang sudah terdaftar', function () {
    [, $kelas] = kelasBerisiSiswa();               // 1 kelas, 1 wali kelas, 1 siswa terdaftar.
    siswaLainDiKelas($kelas->id, 'Siti Aminah', '1234567892', '10003'); // Siswa kedua.

    // Siswa yang datanya sudah diimpor tetapi belum mendaftar akun tidak dihitung:
    // ia belum bisa memakai sistemnya sama sekali.
    siswaBelumPunyaAkun(nisn: '1234567899', nis: '10099');

    Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);

    $admin = Pengguna::factory()->admin()->create([
        'email' => 'admin@sentrisiswa.test',
        'status' => 'registered',
    ]);
    masukSebagai($admin);

    $this->get('/admin/dashboard')
        ->assertSee('Siswa Terdaftar')
        ->assertSeeInOrder(['Siswa Terdaftar', '2'])
        ->assertSeeInOrder(['Guru Terdaftar', '1'])
        ->assertSeeInOrder(['Total Kelas', '2']);
});

// TS.DSB.002 / TC.DSB.002.001 — Positive
test('wali kelas melihat kehadiran kelasnya hari ini di dashboard', function () {
    Carbon::setTestNow('2026-07-06 08:00:00'); // Senin, hari absensi.
    [, $kelas, $siswa] = waliKelasDenganKelas();
    $siswaLain = siswaLainDiKelas($kelas->id, 'Siti Aminah', '1234567892', '10003');

    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');
    catatKehadiran($siswaLain->nisn, '2026-07-06', 'sakit');

    $this->get('/wali-kelas/dashboard')
        ->assertSee('Dashboard Guru')
        ->assertSee('10 IPA 1')
        ->assertSeeInOrder(['Hadir', '1'])
        ->assertSeeInOrder(['Izin/Sakit', '1']);
});

// TS.DSB.003 / TC.DSB.003.001 — Positive
test('guru bk melihat tingkat yang dipegangnya di dashboard', function () {
    kelasBerisiSiswa(); // Kelas 10 IPA 1 berisi satu siswa terdaftar.
    bkMasuk('10');

    $this->get('/bk/dashboard')
        ->assertSee('Dashboard Guru')
        ->assertSeeInOrder(['Tingkat BK', '10'])
        ->assertSeeInOrder(['Kelas', '1'])
        ->assertSeeInOrder(['Siswa', '1']);
});

// TS.DSB.004 / TC.DSB.004.001 — Positive
test('kesiswaan melihat jumlah pengajuan poin yang menunggu keputusannya', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    pengajuanPoinMenunggu($siswa, $wali->id);
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    kesiswaanMasuk();

    $this->get('/kesiswaan/dashboard')
        ->assertSee('Dashboard Guru')
        ->assertSeeInOrder(['Pengajuan Poin Pending', '1'])
        ->assertSeeInOrder(['Pelanggaran Dicatat', '1']);
});

// TS.DSB.005 / TC.DSB.005.001 — Positive
test('siswa melihat sisa poin dan rekap kehadirannya sendiri di dashboard', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    $siswa = siswaMasuk();

    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-07', 'alpha');
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    $this->get('/siswa/dashboard')
        ->assertSee('Dashboard Siswa')
        // Poin awal 100, dipotong 10 karena pelanggaran.
        ->assertSeeInOrder(['Sisa Poin', '90'])
        ->assertSeeInOrder(['Hadir', '1'])
        ->assertSeeInOrder(['Alpha', '1']);
});

// TS.DSB.006 / TC.DSB.006.001 — Negative — alur alternatif: belum punya kelas
test('wali kelas yang belum dipasangi kelas tetap bisa membuka dashboardnya', function () {
    $wali = Pengguna::factory()->homeroom()->create([
        'email' => 'wali.tanpa.kelas@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($wali);

    // Tidak ada ringkasan kelas yang bisa ditampilkan, tetapi halamannya tidak
    // boleh rusak - guru itu masih perlu membuka menu lain dari sini.
    $this->get('/wali-kelas/dashboard')
        ->assertSuccessful()
        ->assertSee('Dashboard Guru')
        ->assertDontSee('10 IPA 1');
});
