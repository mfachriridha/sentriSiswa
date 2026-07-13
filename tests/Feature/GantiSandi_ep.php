<?php

use App\Mail\OtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Ganti Kata Sandi lewat OTP — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: pengguna yang sedang masuk mengganti kata sandinya sendiri
| dari halaman profil. Hasilnya diperiksa dari apa yang muncul di layar, bukan
| dari basis data.
|
| Kata sandi tidak bisa langsung diganti: sistem lebih dulu mengirim kode OTP
| enam angka ke email pengguna. Setelah kodenya benar, barulah halaman kata sandi
| baru terbuka. Kode itu berlaku 10 menit, dan pengiriman ulangnya diberi jeda 60
| detik supaya tidak bisa dipakai membanjiri email.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.GKS.001 / TC.GKS.001.001 — Positive
test('pengguna meminta kode otp untuk mengganti kata sandi', function () {
    Mail::fake();
    waliKelasDenganKelas();

    $this->followingRedirects()
        ->post('/wali-kelas/profil/ganti-sandi')
        ->assertSee('Kode OTP telah dikirim ke email Anda.');

    Mail::assertSent(OtpMail::class);
});

// TS.GKS.002 / TC.GKS.002.001 — Positive
test('pengguna mengganti kata sandi setelah kode otp benar', function () {
    Mail::fake();
    $kode = mintaKodeGantiSandi();

    $this->followingRedirects()
        ->post('/otp/verifikasi', ['otp' => $kode])
        ->assertSee('OTP berhasil diverifikasi. Masukkan kata sandi baru Anda.');

    $this->followingRedirects()
        ->post('/wali-kelas/profil/set-sandi-baru', [
            'password' => 'sandibaru123',
            'password_confirmation' => 'sandibaru123',
        ])
        ->assertSee('Kata sandi berhasil diubah.');
});

// TS.GKS.003 / TC.GKS.003.001 — Positive
test('pengguna bisa masuk memakai kata sandi barunya', function () {
    Mail::fake();
    $kode = mintaKodeGantiSandi();

    $this->post('/otp/verifikasi', ['otp' => $kode]);
    $this->post('/wali-kelas/profil/set-sandi-baru', [
        'password' => 'sandibaru123',
        'password_confirmation' => 'sandibaru123',
    ]);

    $this->post('/logout');

    $this->followingRedirects()
        ->post('/login', [
            'email' => 'wali.kelas@sentrisiswa.test',
            'password' => 'sandibaru123',
        ])
        ->assertSee('Dashboard');
});

// TS.GKS.004 / TC.GKS.004.001 — Negative
test('kode otp yang salah ditolak', function () {
    Mail::fake();
    $kode = mintaKodeGantiSandi();

    $kodeSalah = str_pad((string) ((((int) $kode) + 1) % 1000000), 6, '0', STR_PAD_LEFT);

    $this->from('/otp/verifikasi')
        ->followingRedirects()
        ->post('/otp/verifikasi', ['otp' => $kodeSalah])
        ->assertSee('Kode OTP salah atau sudah kedaluwarsa.');
});

// TS.GKS.005 / TC.GKS.005.001 — Negative
test('kode otp yang sudah kedaluwarsa ditolak', function () {
    Mail::fake();
    Carbon::setTestNow('2026-07-06 08:00:00');
    $kode = mintaKodeGantiSandi();

    // Kode hanya berlaku 10 menit.
    Carbon::setTestNow('2026-07-06 08:11:00');

    $this->from('/otp/verifikasi')
        ->followingRedirects()
        ->post('/otp/verifikasi', ['otp' => $kode])
        ->assertSee('Kode OTP salah atau sudah kedaluwarsa.');
});

// TS.GKS.006 / TC.GKS.006.001 — Negative
test('kode otp yang belum diisi ditolak', function () {
    Mail::fake();
    mintaKodeGantiSandi();

    $this->from('/otp/verifikasi')
        ->followingRedirects()
        ->post('/otp/verifikasi', ['otp' => ''])
        ->assertSee('Masukkan kode OTP terlebih dahulu.');
});

// TS.GKS.007 / TC.GKS.007.001 — Negative
test('halaman kata sandi baru tidak terbuka sebelum kode otp diverifikasi', function () {
    Mail::fake();
    mintaKodeGantiSandi();

    // Belum memasukkan kode apa pun, jadi halaman kata sandi baru belum boleh dibuka.
    $this->followingRedirects()
        ->get('/wali-kelas/profil/set-sandi-baru')
        ->assertDontSee('Kata Sandi Baru');
});

// TS.GKS.008 / TC.GKS.008.001 — Negative
test('konfirmasi kata sandi baru yang tidak sama ditolak', function () {
    Mail::fake();
    $kode = mintaKodeGantiSandi();

    $this->post('/otp/verifikasi', ['otp' => $kode]);

    $this->from('/wali-kelas/profil/set-sandi-baru')
        ->followingRedirects()
        ->post('/wali-kelas/profil/set-sandi-baru', [
            'password' => 'sandibaru123',
            'password_confirmation' => 'sandilain456',
        ])
        ->assertSee('Konfirmasi kata sandi tidak sesuai.');
});

// TS.GKS.009 / TC.GKS.009.001 — Negative
test('kata sandi baru tanpa angka ditolak', function () {
    Mail::fake();
    $kode = mintaKodeGantiSandi();

    $this->post('/otp/verifikasi', ['otp' => $kode]);

    $this->from('/wali-kelas/profil/set-sandi-baru')
        ->followingRedirects()
        ->post('/wali-kelas/profil/set-sandi-baru', [
            'password' => 'sandibarutanpaangka',
            'password_confirmation' => 'sandibarutanpaangka',
        ])
        ->assertSee('Kata sandi harus memuat huruf dan angka.');
});

// TS.GKS.010 / TC.GKS.010.001 — Negative
test('kode otp tidak bisa dikirim ulang sebelum jedanya lewat', function () {
    Mail::fake();
    Carbon::setTestNow('2026-07-06 08:00:00');
    mintaKodeGantiSandi();

    $this->post('/otp/kirim-ulang');

    // Baru 30 detik, padahal jedanya 60 detik.
    Carbon::setTestNow('2026-07-06 08:00:30');

    $this->from('/otp/verifikasi')
        ->followingRedirects()
        ->post('/otp/kirim-ulang')
        ->assertSee('Tunggu 30 detik lagi sebelum mengirim ulang kode.');
});

// TS.GKS.011 / TC.GKS.011.001 — Positive
test('kode otp bisa dikirim ulang setelah jedanya lewat', function () {
    Mail::fake();
    Carbon::setTestNow('2026-07-06 08:00:00');
    mintaKodeGantiSandi();

    $this->post('/otp/kirim-ulang');

    Carbon::setTestNow('2026-07-06 08:01:01');

    $this->from('/otp/verifikasi')
        ->followingRedirects()
        ->post('/otp/kirim-ulang')
        ->assertSee('Kode OTP baru telah dikirim.');
});
