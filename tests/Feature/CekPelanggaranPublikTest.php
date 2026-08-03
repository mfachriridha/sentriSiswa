<?php

use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\Pengguna;
use App\Models\ProfilGuru;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

test('link publik orang tua menampilkan riwayat pelanggaran yang disetujui', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');

    $teacherUser = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    ProfilGuru::factory()->create(['pengguna_id' => $teacherUser->id, 'tipe_guru' => 'wali_kelas']);
    $class = Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10', 'wali_kelas_id' => $teacherUser->id]);

    $studentUser = Pengguna::factory()->student()->create(['status' => 'registered', 'nama' => 'Budi Santoso']);
    $studentProfile = ProfilSiswa::factory()->create([
        'pengguna_id' => $studentUser->id,
        'kelas_id' => $class->id,
    ]);

    // Create 1 approved violation and 1 pending violation
    PelanggaranSiswa::create([
        'profil_siswa_id' => $studentProfile->nisn,
        'tanggal_pelanggaran' => '2026-07-05',
        'nama_pelanggaran' => 'Terlambat Masuk Sekolah',
        'kategori_pelanggaran' => 'ringan',
        'pengurangan_poin' => 5,
        'status' => 'approved',
        'catatan' => 'Terlambat 15 menit',
    ]);

    PelanggaranSiswa::create([
        'profil_siswa_id' => $studentProfile->nisn,
        'tanggal_pelanggaran' => '2026-07-06',
        'nama_pelanggaran' => 'Merokok di Kantin',
        'kategori_pelanggaran' => 'berat',
        'pengurangan_poin' => 20,
        'status' => 'pending',
    ]);

    $link = linkAbsensiOrangTua($class->id, '2026-07-06');

    $this->post($link.'/cek', ['nisn' => $studentProfile->nisn])
        ->assertSuccessful()
        ->assertSee('Riwayat Pelanggaran')
        ->assertSee('Terlambat Masuk Sekolah')
        ->assertSee('-5 Poin')
        ->assertDontSee('Merokok di Kantin');
});

test('link publik orang tua menampilkan pesan ramah jika siswa tidak memiliki pelanggaran', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');

    $teacherUser = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    ProfilGuru::factory()->create(['pengguna_id' => $teacherUser->id, 'tipe_guru' => 'wali_kelas']);
    $class = Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10', 'wali_kelas_id' => $teacherUser->id]);

    $studentUser = Pengguna::factory()->student()->create(['status' => 'registered', 'nama' => 'Siti Aminah']);
    $studentProfile = ProfilSiswa::factory()->create([
        'pengguna_id' => $studentUser->id,
        'kelas_id' => $class->id,
    ]);

    $link = linkAbsensiOrangTua($class->id, '2026-07-06');

    $this->post($link.'/cek', ['nisn' => $studentProfile->nisn])
        ->assertSuccessful()
        ->assertSee('Riwayat Pelanggaran')
        ->assertSee('Tidak Ada Catatan Pelanggaran')
        ->assertSee('Anak Anda memiliki catatan kedisiplinan yang sangat baik!');
});
