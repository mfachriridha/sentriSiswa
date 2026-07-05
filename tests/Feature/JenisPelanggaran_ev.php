<?php

use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function jenisPelanggaranKesiswaan(): Pengguna
{
    $studentAffairs = Pengguna::factory()->studentAffairs()->create(['status' => 'registered']);
    $studentAffairs->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'student_affairs',
    ]);

    return $studentAffairs;
}

// TS.JNP.001 / TC.JNP.001.001 — buat jenis pelanggaran baru berhasil (positive)
test('student affairs can create a new violation type', function () {
    $kesiswaan = jenisPelanggaranKesiswaan();

    $this->actingAs($kesiswaan)->post(route('kesiswaan.jenis-pelanggaran.store'), [
        'nama' => 'Terlambat masuk kelas',
        'kategori' => 'light',
        'pengurangan_poin' => 10,
        'keterangan' => 'Terlambat lebih dari 15 menit.',
        'aktif' => '1',
    ])->assertRedirect(route('kesiswaan.jenis-pelanggaran.index'));

    $this->assertDatabaseHas('jenis_pelanggaran', [
        'nama' => 'Terlambat masuk kelas',
        'kategori' => 'light',
        'pengurangan_poin' => 10,
        'aktif' => true,
    ]);
});

// TS.JNP.002 / TC.JNP.002.001 — nama duplikat ditolak (negative)
test('student affairs cannot create a violation type with a duplicate name', function () {
    $kesiswaan = jenisPelanggaranKesiswaan();
    JenisPelanggaran::factory()->create(['nama' => 'Membolos', 'kategori' => 'medium', 'pengurangan_poin' => 30]);

    $this->actingAs($kesiswaan)->post(route('kesiswaan.jenis-pelanggaran.store'), [
        'nama' => 'Membolos',
        'kategori' => 'medium',
        'pengurangan_poin' => 30,
        'aktif' => '1',
    ])->assertSessionHasErrors('nama');
});

// TS.JNP.003 / TC.JNP.003.001 — kategori tidak valid ditolak (negative)
test('student affairs cannot create a violation type with an invalid category', function () {
    $kesiswaan = jenisPelanggaranKesiswaan();

    $this->actingAs($kesiswaan)->post(route('kesiswaan.jenis-pelanggaran.store'), [
        'nama' => 'Pelanggaran aneh',
        'kategori' => 'extreme',
        'pengurangan_poin' => 10,
        'aktif' => '1',
    ])->assertSessionHasErrors('kategori');
});

// TS.JNP.004 / TC.JNP.004.001 — poin di luar rentang kategori ditolak (negative)
test('student affairs cannot create a violation type with points outside the category range', function () {
    $kesiswaan = jenisPelanggaranKesiswaan();

    $this->actingAs($kesiswaan)->post(route('kesiswaan.jenis-pelanggaran.store'), [
        'nama' => 'Pelanggaran kategori salah',
        'kategori' => 'light',
        'pengurangan_poin' => 60,
        'aktif' => '1',
    ])->assertSessionHasErrors('pengurangan_poin');
});

// TS.JNP.005 / TC.JNP.005.001 — update berhasil (positive)
test('student affairs can update a violation type', function () {
    $kesiswaan = jenisPelanggaranKesiswaan();
    $type = JenisPelanggaran::factory()->create(['nama' => 'Seragam tidak lengkap', 'kategori' => 'light', 'pengurangan_poin' => 10]);

    $this->actingAs($kesiswaan)->put(route('kesiswaan.jenis-pelanggaran.update', $type), [
        'nama' => 'Seragam tidak rapi',
        'kategori' => 'light',
        'pengurangan_poin' => 15,
        'aktif' => '1',
    ])->assertRedirect(route('kesiswaan.jenis-pelanggaran.index'));

    expect($type->fresh()->nama)->toBe('Seragam tidak rapi');
    expect($type->fresh()->pengurangan_poin)->toBe(15);
});

// TS.JNP.006 / TC.JNP.006.001 — update nama jadi duplikat milik jenis lain ditolak (negative)
test('student affairs cannot update a violation type name to one already used by another type', function () {
    $kesiswaan = jenisPelanggaranKesiswaan();
    JenisPelanggaran::factory()->create(['nama' => 'Merokok di area sekolah', 'kategori' => 'heavy', 'pengurangan_poin' => 60]);
    $type = JenisPelanggaran::factory()->create(['nama' => 'Berkelahi', 'kategori' => 'heavy', 'pengurangan_poin' => 65]);

    $this->actingAs($kesiswaan)->put(route('kesiswaan.jenis-pelanggaran.update', $type), [
        'nama' => 'Merokok di area sekolah',
        'kategori' => 'heavy',
        'pengurangan_poin' => 65,
        'aktif' => '1',
    ])->assertSessionHasErrors('nama');
});

// TS.JNP.007 / TC.JNP.007.001 — nonaktifkan lewat update, bukan dihapus (positive)
test('student affairs can deactivate a violation type via update instead of deleting it', function () {
    $kesiswaan = jenisPelanggaranKesiswaan();
    $type = JenisPelanggaran::factory()->create(['nama' => 'Tidak membawa buku', 'kategori' => 'light', 'pengurangan_poin' => 5, 'aktif' => true]);

    $this->actingAs($kesiswaan)->put(route('kesiswaan.jenis-pelanggaran.update', $type), [
        'nama' => 'Tidak membawa buku',
        'kategori' => 'light',
        'pengurangan_poin' => 5,
        'aktif' => '0',
    ])->assertRedirect(route('kesiswaan.jenis-pelanggaran.index'));

    expect($type->fresh()->aktif)->toBeFalse();
    $this->assertDatabaseHas('jenis_pelanggaran', ['id' => $type->id]);
});

// TS.JNP.008 / TC.JNP.008.001 — hapus yang belum pernah dipakai berhasil (positive)
test('student affairs can delete a violation type that has never been used', function () {
    $kesiswaan = jenisPelanggaranKesiswaan();
    $type = JenisPelanggaran::factory()->create(['nama' => 'Tidak memakai atribut', 'kategori' => 'light', 'pengurangan_poin' => 5]);

    $this->actingAs($kesiswaan)->delete(route('kesiswaan.jenis-pelanggaran.destroy', $type))
        ->assertRedirect(route('kesiswaan.jenis-pelanggaran.index'));

    $this->assertDatabaseMissing('jenis_pelanggaran', ['id' => $type->id]);
});

// TS.JNP.009 / TC.JNP.009.001 — hapus yang sudah dipakai ditolak (negative)
test('student affairs cannot delete a violation type already used in a violation record', function () {
    $kesiswaan = jenisPelanggaranKesiswaan();
    $type = JenisPelanggaran::factory()->create(['nama' => 'Bullying', 'kategori' => 'severe', 'pengurangan_poin' => 80]);
    $class = Kelas::create(['nama' => '10. Jenis Pelanggaran 1', 'tingkat' => '10']);
    $studentUser = Pengguna::factory()->student()->create(['status' => 'registered']);
    $student = ProfilSiswa::factory()->create(['pengguna_id' => $studentUser->id, 'kelas_id' => $class->id]);
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'status' => 'approved',
    ]);

    $this->actingAs($kesiswaan)->delete(route('kesiswaan.jenis-pelanggaran.destroy', $type))
        ->assertRedirect(route('kesiswaan.jenis-pelanggaran.index'));

    $this->assertDatabaseHas('jenis_pelanggaran', ['id' => $type->id]);
});

// TS.JNP.010 / TC.JNP.010.001 — filter index berdasarkan kategori (positive)
test('violation type index filters by category', function () {
    $kesiswaan = jenisPelanggaranKesiswaan();
    JenisPelanggaran::factory()->create(['nama' => 'Ringan A', 'kategori' => 'light', 'pengurangan_poin' => 10]);
    JenisPelanggaran::factory()->create(['nama' => 'Berat B', 'kategori' => 'heavy', 'pengurangan_poin' => 60]);

    $this->actingAs($kesiswaan)->get(route('kesiswaan.jenis-pelanggaran.index', ['category' => 'light']))
        ->assertSuccessful()
        ->assertSee('Ringan A')
        ->assertDontSee('Berat B');
});

// TS.JNP.011 / TC.JNP.011.001 — filter index berdasarkan status aktif/nonaktif (positive)
test('violation type index filters by active status', function () {
    $kesiswaan = jenisPelanggaranKesiswaan();
    JenisPelanggaran::factory()->create(['nama' => 'Masih Aktif', 'kategori' => 'light', 'pengurangan_poin' => 10, 'aktif' => true]);
    JenisPelanggaran::factory()->create(['nama' => 'Sudah Nonaktif', 'kategori' => 'light', 'pengurangan_poin' => 10, 'aktif' => false]);

    $this->actingAs($kesiswaan)->get(route('kesiswaan.jenis-pelanggaran.index', ['status' => 'inactive']))
        ->assertSuccessful()
        ->assertSee('Sudah Nonaktif')
        ->assertDontSee('Masih Aktif');
});
