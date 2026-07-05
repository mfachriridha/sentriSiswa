<?php

use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function pelanggaranKesiswaanActor(): Pengguna
{
    $studentAffairs = Pengguna::factory()->studentAffairs()->create(['status' => 'registered']);
    $studentAffairs->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'student_affairs',
    ]);

    return $studentAffairs;
}

function pelanggaranKesiswaanStudent(string $className, string $nisn): ProfilSiswa
{
    $class = Kelas::create(['nama' => $className, 'tingkat' => '10']);
    $studentUser = Pengguna::factory()->student()->create(['status' => 'registered']);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $studentUser->id,
        'kelas_id' => $class->id,
        'nisn' => $nisn,
    ]);
}

// TS.PSK.001 / TC.PSK.001.001 — catat pelanggaran siswa manapun tanpa batas kelas, langsung approved (positive)
test('student affairs can record a violation for any student regardless of class and it is auto-approved', function () {
    $kesiswaan = pelanggaranKesiswaanActor();
    $student = pelanggaranKesiswaanStudent('10. Pelanggaran K 1', '90001');
    $type = JenisPelanggaran::factory()->create(['kategori' => 'light', 'pengurangan_poin' => 10, 'aktif' => true]);

    $this->actingAs($kesiswaan)->post(route('kesiswaan.pelanggaran-siswa.store'), [
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'tanggal_pelanggaran' => now()->format('Y-m-d'),
        'catatan' => 'Kedapatan merokok di kantin.',
    ])->assertRedirect(route('kesiswaan.pelanggaran-siswa.index'));

    $violation = PelanggaranSiswa::where('profil_siswa_id', $student->nisn)->firstOrFail();
    expect($violation->status)->toBe('approved');
    expect($violation->disetujui_oleh_id)->toBe($kesiswaan->id);
});

// TS.PSK.002 / TC.PSK.002.001 — jenis pelanggaran nonaktif ditolak (negative)
test('student affairs cannot record a violation using an inactive violation type', function () {
    $kesiswaan = pelanggaranKesiswaanActor();
    $student = pelanggaranKesiswaanStudent('10. Pelanggaran K 2', '90002');
    $type = JenisPelanggaran::factory()->create(['aktif' => false]);

    $this->actingAs($kesiswaan)->post(route('kesiswaan.pelanggaran-siswa.store'), [
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'tanggal_pelanggaran' => now()->format('Y-m-d'),
    ])->assertSessionHasErrors('jenis_pelanggaran_id');
});

// TS.PSK.003 / TC.PSK.003.001 — jenis_pelanggaran_id tidak ada ditolak (negative)
test('student affairs cannot record a violation with a non-existent violation type id', function () {
    $kesiswaan = pelanggaranKesiswaanActor();
    $student = pelanggaranKesiswaanStudent('10. Pelanggaran K 3', '90003');

    $this->actingAs($kesiswaan)->post(route('kesiswaan.pelanggaran-siswa.store'), [
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => 999999,
        'tanggal_pelanggaran' => now()->format('Y-m-d'),
    ])->assertSessionHasErrors('jenis_pelanggaran_id');
});

// TS.PSK.004 / TC.PSK.004.001 — profil_siswa_id (nisn) tidak ada ditolak (negative)
test('student affairs cannot record a violation for a non-existent student nisn', function () {
    $kesiswaan = pelanggaranKesiswaanActor();
    $type = JenisPelanggaran::factory()->create(['aktif' => true]);

    $this->actingAs($kesiswaan)->post(route('kesiswaan.pelanggaran-siswa.store'), [
        'profil_siswa_id' => '9999999999',
        'jenis_pelanggaran_id' => $type->id,
        'tanggal_pelanggaran' => now()->format('Y-m-d'),
    ])->assertSessionHasErrors('profil_siswa_id');
});

// TS.PSK.005 / TC.PSK.005.001 — tanggal di masa depan ditolak (negative)
test('student affairs cannot record a violation with a future date', function () {
    $kesiswaan = pelanggaranKesiswaanActor();
    $student = pelanggaranKesiswaanStudent('10. Pelanggaran K 4', '90004');
    $type = JenisPelanggaran::factory()->create(['aktif' => true]);

    $this->actingAs($kesiswaan)->post(route('kesiswaan.pelanggaran-siswa.store'), [
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'tanggal_pelanggaran' => now()->addDay()->format('Y-m-d'),
    ])->assertSessionHasErrors('tanggal_pelanggaran');
});

// TS.PSK.006 / TC.PSK.006.001 — update berhasil (positive)
test('student affairs can update an existing violation record', function () {
    $kesiswaan = pelanggaranKesiswaanActor();
    $student = pelanggaranKesiswaanStudent('10. Pelanggaran K 5', '90005');
    $type = JenisPelanggaran::factory()->create(['aktif' => true]);
    $violation = PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'catatan' => 'Catatan lama',
    ]);

    $this->actingAs($kesiswaan)->put(route('kesiswaan.pelanggaran-siswa.update', $violation), [
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'tanggal_pelanggaran' => now()->format('Y-m-d'),
        'catatan' => 'Catatan baru',
    ])->assertRedirect(route('kesiswaan.pelanggaran-siswa.index'));

    expect($violation->fresh()->catatan)->toBe('Catatan baru');
});

// TS.PSK.007 / TC.PSK.007.001 — update tetap boleh pertahankan jenis pelanggaran nonaktif yang sedang dipakai (positive)
test('student affairs can keep the currently assigned inactive violation type when updating', function () {
    $kesiswaan = pelanggaranKesiswaanActor();
    $student = pelanggaranKesiswaanStudent('10. Pelanggaran K 6', '90006');
    $type = JenisPelanggaran::factory()->create(['aktif' => true]);
    $violation = PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
    ]);
    $type->update(['aktif' => false]);

    $this->actingAs($kesiswaan)->put(route('kesiswaan.pelanggaran-siswa.update', $violation), [
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'tanggal_pelanggaran' => now()->format('Y-m-d'),
        'catatan' => 'Tetap pakai jenis nonaktif ini',
    ])->assertRedirect(route('kesiswaan.pelanggaran-siswa.index'));

    expect($violation->fresh()->catatan)->toBe('Tetap pakai jenis nonaktif ini');
});

// TS.PSK.008 / TC.PSK.008.001 — hapus catatan pelanggaran berhasil (positive)
test('student affairs can delete a violation record', function () {
    $kesiswaan = pelanggaranKesiswaanActor();
    $student = pelanggaranKesiswaanStudent('10. Pelanggaran K 7', '90007');
    $violation = PelanggaranSiswa::factory()->create(['profil_siswa_id' => $student->nisn]);

    $this->actingAs($kesiswaan)->delete(route('kesiswaan.pelanggaran-siswa.destroy', $violation))
        ->assertRedirect(route('kesiswaan.pelanggaran-siswa.index'));

    $this->assertDatabaseMissing('pelanggaran_siswa', ['id' => $violation->id]);
});

// TS.PSK.009 / TC.PSK.009.001 — filter index berdasarkan kelas, kategori, jenis, tanggal, status (positive)
test('violation record index filters by class, category, type, date, and status', function () {
    $kesiswaan = pelanggaranKesiswaanActor();
    $class = Kelas::create(['nama' => '10. Pelanggaran K 8', 'tingkat' => '10']);
    $studentUserA = Pengguna::factory()->student()->create(['status' => 'registered', 'nama' => 'Gilang Ramadhan']);
    $studentA = ProfilSiswa::factory()->create(['pengguna_id' => $studentUserA->id, 'kelas_id' => $class->id, 'nisn' => '90008']);
    $typeA = JenisPelanggaran::factory()->create(['nama' => 'Terlambat Filter', 'kategori' => 'light', 'pengurangan_poin' => 10]);
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $studentA->nisn,
        'jenis_pelanggaran_id' => $typeA->id,
        'nama_pelanggaran' => $typeA->nama,
        'kategori_pelanggaran' => 'light',
        'pengurangan_poin' => 10,
        'tanggal_pelanggaran' => '2026-01-10',
        'status' => 'approved',
    ]);

    $otherClass = Kelas::create(['nama' => '11. Pelanggaran K 9', 'tingkat' => '11']);
    $studentUserB = Pengguna::factory()->student()->create(['status' => 'registered', 'nama' => 'Hesti Purnama']);
    $studentB = ProfilSiswa::factory()->create(['pengguna_id' => $studentUserB->id, 'kelas_id' => $otherClass->id, 'nisn' => '90009']);
    $typeB = JenisPelanggaran::factory()->create(['nama' => 'Bullying Filter', 'kategori' => 'severe', 'pengurangan_poin' => 80]);
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $studentB->nisn,
        'jenis_pelanggaran_id' => $typeB->id,
        'nama_pelanggaran' => $typeB->nama,
        'kategori_pelanggaran' => 'severe',
        'pengurangan_poin' => 80,
        'tanggal_pelanggaran' => '2026-02-15',
        'status' => 'approved',
    ]);

    // Nama jenis pelanggaran selalu muncul di dropdown filter (tidak ter-scope), jadi assertion pakai nama siswa yang cuma tampil di baris tabel.
    $this->actingAs($kesiswaan)->get(route('kesiswaan.pelanggaran-siswa.index', ['kelas_id' => $class->id]))
        ->assertSuccessful()->assertSee('Gilang Ramadhan')->assertDontSee('Hesti Purnama');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.pelanggaran-siswa.index', ['kategori' => 'severe']))
        ->assertSuccessful()->assertSee('Hesti Purnama')->assertDontSee('Gilang Ramadhan');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.pelanggaran-siswa.index', ['jenis_pelanggaran_id' => $typeA->id]))
        ->assertSuccessful()->assertSee('Gilang Ramadhan')->assertDontSee('Hesti Purnama');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.pelanggaran-siswa.index', ['tanggal_pelanggaran' => '2026-01-10']))
        ->assertSuccessful()->assertSee('Gilang Ramadhan')->assertDontSee('Hesti Purnama');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.pelanggaran-siswa.index', ['status' => 'approved']))
        ->assertSuccessful()->assertSee('Gilang Ramadhan')->assertSee('Hesti Purnama');
});
