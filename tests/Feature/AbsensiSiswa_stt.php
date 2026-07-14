<?php

use App\Models\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Absensi (Siswa) — State Transition Testing
|--------------------------------------------------------------------------
|
| Keadaan absensi seorang siswa pada satu hari:
|
|   belum absen --(siswa absen di dalam jam absen)--> hadir
|   belum absen --(jam absen lewat, tak ada yang datang)--> alpha
|   alpha       --(absen selagi jam absen masih dibuka)--> hadir
|   hadir       --(absen lagi)--> DITOLAK
|   izin/sakit  --(absen lagi)--> DITOLAK
|
| Alpha bukan keadaan mati: ia hanya berarti "sampai detik ini belum ada yang datang".
| Selama jam absennya masih dibuka - misalnya karena admin memperpanjangnya, atau
| karena perintah terjadwal sempat mendahului siswa yang sedang mengirim selfie-nya -
| siswa itu masih boleh absen dan statusnya kembali jadi Hadir.
|
| Izin dan Sakit lain perkara: keduanya ditetapkan wali kelas, dan siswa tidak boleh
| menimpanya.
|
| Ditambah keadaan jendela absensinya sendiri: belum dibuka → dibuka → ditutup,
| dan hari yang memang bukan hari absensi. Tombol absen hanya hidup dalam keadaan
| "dibuka"; di luar itu absensi ditolak berapa pun bagusnya selfie yang dikirim.
|
| Pada pengujian ini jam absen dibuka pukul 06:30 sampai 07:00.
|
*/

beforeEach(function () {
    Storage::fake('public');
    Pengaturan::set('attendance_start_time', '06:30');
    Pengaturan::set('attendance_end_time', '07:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

// TS.ABS.002 / TC.ABS.002.001 — Negative — jendela belum dibuka
test('siswa gagal absen sebelum jam absen dibuka', function () {
    Carbon::setTestNow('2026-07-06 06:00:00');
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Waktu absen sudah lewat atau belum dimulai.');
});

// TS.ABS.003 / TC.ABS.003.001 — Negative — jendela sudah ditutup
test('siswa gagal absen setelah jam absen berakhir', function () {
    Carbon::setTestNow('2026-07-06 07:30:00');
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Waktu absen sudah lewat atau belum dimulai.');
});

// TS.ABS.004 / TC.ABS.004.001 — Negative — hari yang jendelanya tidak pernah dibuka
test('siswa gagal absen di hari yang bukan hari absensi', function () {
    Carbon::setTestNow('2026-07-05 06:35:00'); // Minggu.
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Absensi hanya tersedia pada hari '.Pengaturan::labelHariAbsen().'.');
});

// TS.ABS.005 / TC.ABS.005.001 — Negative — hadir → absen lagi
test('siswa tidak bisa absen dua kali dalam sehari', function () {
    Carbon::setTestNow('2026-07-06 06:35:00');
    siswaMasuk();

    $this->post('/siswa/absensi', ['selfie' => selfieAbsensi()]);

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Anda sudah absen hari ini.');
});

// TS.ABS.014 / TC.ABS.014.001 — Negative — belum absen → alpha, tanpa disentuh siapa pun
test('siswa yang tidak absen sampai jam absen berakhir tercatat alpha', function () {
    Carbon::setTestNow('2026-07-06 06:00:00'); // Senin pagi, sebelum jam absen.
    siswaMasuk();

    // Sekolah menyiapkan catatan harian tiap pagi: semua siswa "belum absen".
    $this->artisan('attendance:create-daily');

    $this->get('/siswa/absensi')->assertSee('Belum waktunya absen. Absen dimulai pukul 06:30.');

    // Jam absen lewat tanpa siswa itu absen sama sekali.
    Carbon::setTestNow('2026-07-06 07:05:00');
    $this->artisan('attendance:update-unmarked');

    // Statusnya berpindah sendiri jadi Alpha, dan siswa membacanya di riwayatnya.
    $this->get('/siswa/absensi/riwayat')
        ->assertSee('Alpha');
});

// TS.ABS.017 / TC.ABS.017.001 — Positive — alpha → hadir, selagi jam absen masih dibuka
test('siswa yang terlanjur dicap alpha tetap bisa absen selama jam absennya masih dibuka', function () {
    Carbon::setTestNow('2026-07-06 06:35:00');
    $siswa = siswaMasuk();

    // Ia terlanjur dicap Alpha - misalnya jam absen tadinya ditutup pukul 06:30, lalu
    // admin memperpanjangnya sampai 07:00 karena ada upacara.
    catatKehadiran($siswa->nisn, '2026-07-06', 'alpha');

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Absen berhasil: Hadir.')
        ->assertDontSee('Anda sudah absen hari ini.');

    $this->get('/siswa/absensi/riwayat')
        ->assertSee('Hadir');
});

// TS.ABS.018 / TC.ABS.018.001 — Negative — izin ditetapkan wali kelas, tak bisa ditimpa siswa
test('siswa tidak bisa menimpa status izin yang sudah ditetapkan wali kelasnya', function () {
    Carbon::setTestNow('2026-07-06 06:35:00');
    $siswa = siswaMasuk();

    // Wali kelas sudah menandainya izin karena surat izinnya sudah masuk.
    catatKehadiran($siswa->nisn, '2026-07-06', 'izin');

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Anda sudah absen hari ini.')
        ->assertDontSee('Absen berhasil: Hadir.');
});

// TS.ABS.015 / TC.ABS.015.001 — Positive — hadir tidak ikut berubah jadi alpha
test('siswa yang sudah absen tidak ikut berubah jadi alpha saat jam absen berakhir', function () {
    Carbon::setTestNow('2026-07-06 06:35:00');
    siswaMasuk();

    $this->artisan('attendance:create-daily');

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Absen berhasil: Hadir.');

    Carbon::setTestNow('2026-07-06 07:05:00');
    $this->artisan('attendance:update-unmarked');

    // Yang sudah hadir tetap hadir: perintah itu hanya menyentuh yang belum absen.
    $this->get('/siswa/absensi/riwayat')
        ->assertSee('Hadir')
        ->assertDontSee('Alpha');
});
