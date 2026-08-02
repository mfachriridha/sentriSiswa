<?php

use App\Models\Absensi;
use App\Models\Pengaturan;
use App\Models\Pengguna;
use App\Models\PesanWhatsapp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

/*
|--------------------------------------------------------------------------
| Fitur WhatsApp API (Admin) — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengatur
| pengiriman WhatsApp seperti pengguna biasa. Hasilnya diperiksa dari apa yang
| muncul di layar, bukan dari basis data.
|
| Sekolah memakai layanan luar untuk mengirim WhatsApp. Admin menyimpan token
| layanan itu, lalu bisa mengirim pesan uji untuk memastikan pengiriman
| berjalan. Pada pengujian ini layanan luar tersebut ditiru, sehingga tidak ada
| pesan yang benar-benar terkirim ke nomor siapa pun.
| Keadaan yang menentukan: layanan pengirim hanya hidup setelah tokennya dipasang, dan
| mati lagi begitu tokennya dihapus. Pesan uji ke nomor yang sama juga diberi jeda,
| dan pesan yang gagal terkirim bisa dikirim ulang dari riwayatnya.
|
*/

// TS.WAP.002 / TC.WAP.002.001 — Positive
test('admin berhasil menghapus token yang tersimpan', function () {
    adminWhatsapp();
    Pengaturan::set('fonnte_token', 'token-lama');

    $this->followingRedirects()
        ->put('/admin/pengaturan/whatsapp', [
            'clear_fonnte_token' => '1',
        ])
        ->assertSee('Token Fonnte berhasil dihapus.');
});

// TS.WAP.003 / TC.WAP.003.001 — Positive
test('admin berhasil mengirim pesan uji setelah token dipasang', function () {
    adminWhatsapp();
    Pengaturan::set('fonnte_token', 'token-yang-sah');
    layananWhatsappBerhasil();

    $this->postJson('/admin/pengaturan/whatsapp/test', [
        'phone' => '081234567890',
        'message' => 'Pesan uji dari sekolah.',
    ])->assertSee('true');
});

// TS.WAP.004 / TC.WAP.004.001 — Negative
test('admin gagal mengirim pesan uji karena token belum dipasang', function () {
    adminWhatsapp();
    Pengaturan::set('fonnte_token', '');

    $this->postJson('/admin/pengaturan/whatsapp/test', [
        'phone' => '081234567890',
        'message' => 'Pesan uji dari sekolah.',
    ])->assertSee('Token Fonnte belum dikonfigurasi. Isi token di pengaturan WhatsApp.');
});

// TS.WAP.005 / TC.WAP.005.001 — Negative
test('admin gagal mengirim pesan uji berkali kali ke nomor yang sama dalam waktu singkat', function () {
    adminWhatsapp();
    Pengaturan::set('fonnte_token', 'token-yang-sah');
    layananWhatsappBerhasil();

    $this->postJson('/admin/pengaturan/whatsapp/test', [
        'phone' => '081234567890',
        'message' => 'Pesan uji pertama.',
    ]);

    // Pengiriman kedua ke nomor yang sama langsung sesudahnya ditahan.
    $this->postJson('/admin/pengaturan/whatsapp/test', [
        'phone' => '081234567890',
        'message' => 'Pesan uji kedua.',
    ])->assertSee('Nomor ini baru saja dipakai untuk test. Tunggu sebelum mengirim ulang.');
});

// TS.WAP.007 / TC.WAP.007.001 — Positive
test('admin mengirim ulang pesan whatsapp dari riwayat', function () {
    adminWhatsapp();
    Pengaturan::set('fonnte_token', 'token-yang-sah');
    layananWhatsappBerhasil();

    $pesan = PesanWhatsapp::create([
        'telepon_penerima' => '081234567890',
        'nama_penerima' => 'Raka Pradipta',
        'tipe_pesan' => 'attendance_report',
        'isi_pesan' => 'Laporan absensi harian.',
        'status' => 'failed',
    ]);

    $this->followingRedirects()
        ->post("/admin/pengaturan/whatsapp/riwayat/{$pesan->id}/kirim-ulang")
        ->assertSee('Pesan sedang diproses ulang.');
});

// TS.WAP.011 / TC.WAP.011.001 — Negative
test('admin gagal mengirim ulang pesan yang dibuat di hari lain', function () {
    Carbon::setTestNow('2026-07-05 08:00:00');
    $pesan = PesanWhatsapp::create([
        'telepon_penerima' => '081234567890',
        'nama_penerima' => 'Raka Pradipta',
        'tipe_pesan' => 'attendance_report',
        'isi_pesan' => 'Laporan absensi harian.',
        'status' => 'failed',
    ]);

    Carbon::setTestNow('2026-07-06 08:00:00');
    adminWhatsapp();
    Pengaturan::set('fonnte_token', 'token-yang-sah');
    layananWhatsappBerhasil();

    $this->followingRedirects()
        ->post("/admin/pengaturan/whatsapp/riwayat/{$pesan->id}/kirim-ulang")
        ->assertSee('Pesan ini dibuat di hari lain, datanya sudah kedaluwarsa. Kirim ulang tidak tersedia untuk pesan lama.');
});

// TS.WAP.012 / TC.WAP.012.001 — Positive
test('kirim ulang laporan absensi menyusun ulang isi pesan dari kondisi absensi terkini', function () {
    Carbon::setTestNow('2026-07-06 08:00:00'); // Senin, hari absensi.
    [$wali, $kelas, $siswa] = kelasBerisiSiswa();
    catatKehadiran($siswa->nisn, '2026-07-06', 'alpha');

    $pesan = PesanWhatsapp::create([
        'kelas_id' => $kelas->id,
        'telepon_penerima' => '081234567890',
        'nama_penerima' => $wali->nama,
        'tipe_pesan' => 'attendance_report',
        'isi_pesan' => 'Isi lama: Ahmad Fauzi belum absen.',
        'status' => 'sent',
        'dikirim_pada' => now(),
    ]);

    // Wali kelas mengoreksi status siswa dari Alpha jadi Hadir setelah laporan
    // pertama terkirim.
    Absensi::where('profil_siswa_id', $siswa->nisn)
        ->whereDate('tanggal', '2026-07-06')
        ->update(['status' => 'hadir']);

    adminWhatsapp();
    Pengaturan::set('fonnte_token', 'token-yang-sah');
    layananWhatsappBerhasil();

    // Isi pesan yang dikirim ulang mengikuti kondisi absensi SEKARANG (semua
    // sudah hadir), bukan teks lama yang masih menyebut siswa itu belum absen.
    $this->followingRedirects()
        ->post("/admin/pengaturan/whatsapp/riwayat/{$pesan->id}/kirim-ulang")
        ->assertSee('Seluruh siswa telah presensi hari ini')
        ->assertDontSee('Isi lama');
});
