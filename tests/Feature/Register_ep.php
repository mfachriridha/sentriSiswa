<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Daftar Akun — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pendaftaran berjalan dua tahap:
|   1. Verifikasi identitas — pilih peran, isi nomor identitas.
|   2. Lengkapi pendaftaran — isi email, kata sandi, dan nomor HP untuk guru.
|
| Yang diuji di berkas ini adalah nilai yang diisi di kedua tahap itu: nomor
| identitas (ditemukan, tidak ditemukan, berisi huruf), email (baru, sudah dipakai
| akun lain), dan kata sandi (mengandung angka, konfirmasinya cocok).
|
| Akun yang sudah pernah didaftarkan ada di Register_stt.php, dan alur daftar
| sampai bisa masuk ada di Register_uc.php.
|
| Catatan mengenai kolom yang dikosongkan dan format email yang salah:
| Semua kolom pada formulir ditandai wajib, dan kolom email bertipe email.
| Peramban menahan pengiriman formulir lebih dulu untuk kasus itu, sehingga
| pengguna tidak pernah sampai melihat pesan dari sistem. Karena tidak pernah
| dialami pengguna, kasus tersebut tidak didokumentasikan.
|
*/

// TS.REG.001 / TC.REG.001.001 — Positive
test('siswa berhasil mendaftar menggunakan nisn', function () {
    siswaBelumPunyaAkun(nisn: '1234567890', nis: '10001');

    $this->get('/daftar')->assertSee('Nomor Identitas');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '1234567890',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'siswa.baru@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
        ])
        ->assertSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
});

// TS.REG.002 / TC.REG.002.001 — Positive
test('siswa berhasil mendaftar menggunakan nis', function () {
    siswaBelumPunyaAkun(nisn: '1234567891', nis: '10002');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '10002',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'siswa.nis@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
        ])
        ->assertSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
});

// TS.REG.003 / TC.REG.003.001 — Positive
test('guru berhasil mendaftar dan diminta mengisi nomor hp', function () {
    guruBelumPunyaAkun(nip: '198501012020121001');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'teacher',
        'identity' => '198501012020121001',
    ])->assertSee('Nomor HP');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'guru.baru@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
            'telepon' => '081234567890',
        ])
        ->assertSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
});

// TS.REG.005 / TC.REG.005.001 — Negative
test('pendaftaran ditolak karena nomor identitas siswa tidak ditemukan', function () {
    $this->from('/daftar')
        ->followingRedirects()
        ->post('/daftar/verifikasi', [
            'peran' => 'student',
            'identity' => '9999999999',
        ])
        ->assertSee('NISN/NIS tidak ditemukan.')
        ->assertDontSee('Lengkapi Profil');
});

// TS.REG.006 / TC.REG.006.001 — Negative
test('pendaftaran ditolak karena nomor identitas guru tidak ditemukan', function () {
    $this->from('/daftar')
        ->followingRedirects()
        ->post('/daftar/verifikasi', [
            'peran' => 'teacher',
            'identity' => '199999999999999999',
        ])
        ->assertSee('NIP tidak ditemukan.')
        ->assertDontSee('Lengkapi Profil');
});

// TS.REG.009 / TC.REG.009.001 — Negative
test('pendaftaran ditolak karena nomor identitas berisi huruf', function () {
    $this->from('/daftar')
        ->followingRedirects()
        ->post('/daftar/verifikasi', [
            'peran' => 'student',
            'identity' => 'ABC123',
        ])
        ->assertSee('NIP, NISN, atau NIS hanya boleh berisi angka.');
});

// TS.REG.010 / TC.REG.010.001 — Negative
test('pendaftaran ditolak karena email sudah dipakai akun lain', function () {
    siswaBelumPunyaAkun(nisn: '1234567894', nis: '10005');

    Pengguna::factory()->student()->create([
        'email' => 'sudah.dipakai@sentrisiswa.test',
        'status' => 'registered',
    ]);

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '1234567894',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'sudah.dipakai@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
        ])
        ->assertSee('Email sudah terdaftar.')
        // Pendaftarnya tetap di formulir langkah 2, bukan dilempar balik ke
        // langkah 1 dengan formulir kosong.
        ->assertSee('Lengkapi Profil')
        ->assertDontSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
});

// TS.REG.011 / TC.REG.011.001 — Negative
test('pendaftaran ditolak karena kata sandi tidak mengandung angka', function () {
    siswaBelumPunyaAkun(nisn: '1234567895', nis: '10006');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '1234567895',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'tanpa.angka@sentrisiswa.test',
            'password' => 'RahasiaSaja',
            'password_confirmation' => 'RahasiaSaja',
        ])
        ->assertSee('Kata sandi harus mengandung huruf dan angka.')
        ->assertSee('Lengkapi Profil');
});

// TS.REG.012 / TC.REG.012.001 — Negative
test('pendaftaran ditolak karena ulangi kata sandi tidak sama', function () {
    siswaBelumPunyaAkun(nisn: '1234567896', nis: '10007');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '1234567896',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'beda.sandi@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia456',
        ])
        ->assertSee('Konfirmasi kata sandi tidak cocok.')
        ->assertSee('Lengkapi Profil');
});

// TS.REG.016 / TC.REG.016.001 — Negative
test('pendaftar yang salah mengisi kata sandi tetap di formulirnya, tidak dilempar balik', function () {
    siswaBelumPunyaAkun(nisn: '1234567897', nis: '10008');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '1234567897',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'sandi.salah@sentrisiswa.test',
            'password' => 'rahasiasaja',
            'password_confirmation' => 'rahasiasaja',
        ])
        // Alasan penolakannya terbaca, dan identitas yang tadi diverifikasi masih
        // tertera. Bukan dilempar balik ke formulir nomor identitas yang kosong.
        ->assertSee('Kata sandi harus mengandung huruf dan angka.')
        ->assertSee('Lengkapi Profil')
        ->assertSee('1234567897')
        ->assertDontSee('Verifikasi Identitas');
});

// TS.REG.017 / TC.REG.017.001 — Negative
test('guru yang salah mengetik nip dilempar balik dengan pilihan Guru yang tetap terpilih', function () {
    $this->from('/daftar')
        ->followingRedirects()
        ->post('/daftar/verifikasi', [
            'peran' => 'teacher',
            'identity' => '199999999999999999',
        ])
        ->assertSee('NIP tidak ditemukan.')
        // Pilihan perannya tidak boleh berubah sendiri jadi Siswa: kalau berubah,
        // guru yang mengetik ulang NIP-nya justru dicari di data siswa, lalu
        // diberi tahu "NISN/NIS tidak ditemukan" padahal NIP-nya benar.
        ->assertSee('<input type="radio" name="peran" value="teacher" checked', escape: false);
});
