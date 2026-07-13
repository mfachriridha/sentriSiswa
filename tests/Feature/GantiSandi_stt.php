<?php

use App\Mail\OtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Ganti Kata Sandi lewat OTP — State Transition Testing
|--------------------------------------------------------------------------
|
| Daur hidup kode OTP:
|
|   belum ada kode --(minta kode)------> terkirim
|   terkirim       --(kode benar)------> terverifikasi
|   terkirim       --(lewat 10 menit)--> kedaluwarsa
|   terverifikasi  --(sandi disimpan)--> terpakai
|   terpakai       --(dipakai lagi)----> DITOLAK
|
| Yang menentukan diterima atau ditolaknya bukan angka yang diketik, melainkan
| keadaan kode itu saat diperiksa. Kode yang sama persis bisa diterima sekarang
| dan ditolak semenit kemudian.
|
| Pengiriman ulangnya juga punya keadaan sendiri: baru boleh dilakukan setelah
| jeda 60 detik lewat.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.GKS.001 / TC.GKS.001.001 — Positive — belum ada kode → terkirim
test('pengguna meminta kode otp untuk mengganti kata sandi', function () {
    Mail::fake();
    waliKelasDenganKelas();

    $this->followingRedirects()
        ->post('/wali-kelas/profil/ganti-sandi')
        ->assertSee('Kode OTP telah dikirim ke email Anda.');

    Mail::assertSent(OtpMail::class);
});

// TS.GKS.002 / TC.GKS.002.001 — Positive — terkirim → terverifikasi → terpakai
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

// TS.GKS.003 / TC.GKS.003.001 — Positive — kata sandi lama mati, yang baru hidup
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

// TS.GKS.005 / TC.GKS.005.001 — Negative — terkirim → kedaluwarsa
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

// TS.GKS.007 / TC.GKS.007.001 — Negative — melompati keadaan "terverifikasi"
test('halaman kata sandi baru tidak terbuka sebelum kode otp diverifikasi', function () {
    Mail::fake();
    mintaKodeGantiSandi();

    // Belum memasukkan kode apa pun, jadi halaman kata sandi baru belum boleh dibuka.
    $this->followingRedirects()
        ->get('/wali-kelas/profil/set-sandi-baru')
        ->assertDontSee('Kata Sandi Baru');
});

// TS.GKS.010 / TC.GKS.010.001 — Negative — kirim ulang sebelum jedanya lewat
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

// TS.GKS.011 / TC.GKS.011.001 — Positive — kirim ulang setelah jedanya lewat
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

// TS.GKS.015 / TC.GKS.015.001 — Negative — terpakai → dipakai lagi
test('kode otp yang sudah terpakai tidak bisa dipakai untuk kedua kalinya', function () {
    Mail::fake();
    $kode = mintaKodeGantiSandi();

    $this->post('/otp/verifikasi', ['otp' => $kode]);
    $this->followingRedirects()
        ->post('/wali-kelas/profil/set-sandi-baru', [
            'password' => 'sandibaru123',
            'password_confirmation' => 'sandibaru123',
        ])
        ->assertSee('Kata sandi berhasil diubah.');

    // Kode yang sama diketik ulang. Sekali dipakai, kode itu harus mati - kalau
    // tidak, siapa pun yang sempat melihat kodenya bisa membuka lagi halaman
    // kata sandi baru dan mengganti kata sandi orang itu.
    $this->from('/otp/verifikasi')
        ->followingRedirects()
        ->post('/otp/verifikasi', ['otp' => $kode])
        ->assertSee('Sesi OTP tidak valid. Silakan coba lagi.')
        ->assertDontSee('Masukkan kata sandi baru Anda.');
});
