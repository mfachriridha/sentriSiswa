<?php

use App\Models\Pengaturan;
use App\Models\Pengguna;
use App\Models\PesanWhatsapp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur WhatsApp API (Admin) — Equivalence Partitioning
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
|
*/

function adminWhatsapp(): Pengguna
{
    $admin = Pengguna::factory()->admin()->create([
        'email' => 'admin.whatsapp@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($admin);

    return $admin;
}

/** Meniru layanan pengirim WhatsApp yang berhasil mengirim. */
function layananWhatsappBerhasil(): void
{
    Http::fake([
        'api.fonnte.com/*' => Http::response(['status' => true, 'id' => ['123']], 200),
    ]);
}

// TS.WAP.001 / TC.WAP.001.001 — Positive
test('admin berhasil menyimpan token layanan pengirim whatsapp', function () {
    adminWhatsapp();

    $this->get('/admin/pengaturan/whatsapp')->assertSee('WhatsApp');

    $this->followingRedirects()
        ->put('/admin/pengaturan/whatsapp', [
            'fonnte_token' => 'token-rahasia-sekolah',
        ])
        ->assertSee('Konfigurasi WhatsApp berhasil disimpan.');
});

// TS.WAP.002 / TC.WAP.002.001 — Positive
test('admin berhasil menghapus token yang tersimpan', function () {
    adminWhatsapp();
    Pengaturan::set('fonnte_token', 'token-lama');

    $this->followingRedirects()
        ->put('/admin/pengaturan/whatsapp', [
            'clear_fonnte_token' => '1',
        ])
        ->assertSee('Konfigurasi WhatsApp berhasil disimpan.');
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

// TS.WAP.006 / TC.WAP.006.001 — Positive
test('admin melihat riwayat pesan whatsapp yang pernah dikirim', function () {
    adminWhatsapp();

    PesanWhatsapp::create([
        'telepon_penerima' => '081234567890',
        'nama_penerima' => 'Raka Pradipta',
        'tipe_pesan' => 'attendance_report',
        'isi_pesan' => 'Laporan absensi harian.',
        'status' => 'sent',
        'dikirim_pada' => now(),
    ]);

    $this->get('/admin/pengaturan/whatsapp/riwayat')
        ->assertSee('Riwayat Pengiriman Pesan')
        ->assertSee('Raka Pradipta')
        ->assertSee('Terkirim');
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

// TS.WAP.008 / TC.WAP.008.001 — Positive
test('admin menyaring riwayat pesan berdasarkan status pengiriman', function () {
    adminWhatsapp();

    PesanWhatsapp::create([
        'telepon_penerima' => '081234567890',
        'nama_penerima' => 'Penerima Berhasil',
        'tipe_pesan' => 'attendance_report',
        'isi_pesan' => 'Laporan absensi harian.',
        'status' => 'sent',
        'dikirim_pada' => now(),
    ]);

    PesanWhatsapp::create([
        'telepon_penerima' => '081234567891',
        'nama_penerima' => 'Penerima Gagal',
        'tipe_pesan' => 'attendance_report',
        'isi_pesan' => 'Laporan absensi harian.',
        'status' => 'failed',
    ]);

    $this->get('/admin/pengaturan/whatsapp/riwayat?status=failed')
        ->assertSee('Penerima Gagal')
        ->assertDontSee('Penerima Berhasil');
});
