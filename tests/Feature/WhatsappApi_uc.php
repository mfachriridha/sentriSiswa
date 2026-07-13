<?php

use App\Models\Pengguna;
use App\Models\PesanWhatsapp;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur WhatsApp API (Admin) — Use Case Testing
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
| Alur pemakaian tanpa isian: admin membaca riwayat pesan WhatsApp yang pernah dikirim
| sekolah.
|
*/

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
