<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function jenisPelanggaranBvaKesiswaan(): Pengguna
{
    $studentAffairs = Pengguna::factory()->studentAffairs()->create(['status' => 'registered']);
    $studentAffairs->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'kesiswaan',
    ]);

    return $studentAffairs;
}

// ── Boundary: pengurangan_poin per kategori "light" (rentang 5-25) ────────

// TS.JNP.012 / TC.JNP.012.001 — poin 4 (di bawah batas bawah light, ditolak)
test('violation type rejects points 1 below the light category minimum', function () {
    $kesiswaan = jenisPelanggaranBvaKesiswaan();

    $this->actingAs($kesiswaan)->post(route('kesiswaan.jenis-pelanggaran.store'), [
        'nama' => 'Boundary Light Bawah 1',
        'kategori' => 'ringan',
        'pengurangan_poin' => 4,
        'aktif' => '1',
    ])->assertSessionHasErrors('pengurangan_poin');
});

// TS.JNP.013 / TC.JNP.013.001 — poin 5 (tepat batas bawah light, diperbolehkan)
test('violation type accepts points exactly at the light category minimum', function () {
    $kesiswaan = jenisPelanggaranBvaKesiswaan();

    $this->actingAs($kesiswaan)->post(route('kesiswaan.jenis-pelanggaran.store'), [
        'nama' => 'Boundary Light Bawah 2',
        'kategori' => 'ringan',
        'pengurangan_poin' => 5,
        'aktif' => '1',
    ])->assertRedirect(route('kesiswaan.jenis-pelanggaran.index'));

    $this->assertDatabaseHas('jenis_pelanggaran', ['nama' => 'Boundary Light Bawah 2', 'pengurangan_poin' => 5]);
});

// TS.JNP.014 / TC.JNP.014.001 — poin 25 (tepat batas atas light, diperbolehkan)
test('violation type accepts points exactly at the light category maximum', function () {
    $kesiswaan = jenisPelanggaranBvaKesiswaan();

    $this->actingAs($kesiswaan)->post(route('kesiswaan.jenis-pelanggaran.store'), [
        'nama' => 'Boundary Light Atas 1',
        'kategori' => 'ringan',
        'pengurangan_poin' => 25,
        'aktif' => '1',
    ])->assertRedirect(route('kesiswaan.jenis-pelanggaran.index'));

    $this->assertDatabaseHas('jenis_pelanggaran', ['nama' => 'Boundary Light Atas 1', 'pengurangan_poin' => 25]);
});

// TS.JNP.015 / TC.JNP.015.001 — poin 26 (di atas batas atas light, ditolak)
test('violation type rejects points 1 above the light category maximum', function () {
    $kesiswaan = jenisPelanggaranBvaKesiswaan();

    $this->actingAs($kesiswaan)->post(route('kesiswaan.jenis-pelanggaran.store'), [
        'nama' => 'Boundary Light Atas 2',
        'kategori' => 'ringan',
        'pengurangan_poin' => 26,
        'aktif' => '1',
    ])->assertSessionHasErrors('pengurangan_poin');
});

// ── Boundary: nama max:255 ─────────────────────────────────────────────────

// TS.JNP.016 / TC.JNP.016.001 — nama tepat 255 karakter (diperbolehkan)
test('violation type accepts a name with exactly 255 characters', function () {
    $kesiswaan = jenisPelanggaranBvaKesiswaan();
    $nama255 = str_repeat('a', 255);

    $this->actingAs($kesiswaan)->post(route('kesiswaan.jenis-pelanggaran.store'), [
        'nama' => $nama255,
        'kategori' => 'ringan',
        'pengurangan_poin' => 10,
        'aktif' => '1',
    ])->assertRedirect(route('kesiswaan.jenis-pelanggaran.index'));

    $this->assertDatabaseHas('jenis_pelanggaran', ['nama' => $nama255]);
});

// TS.JNP.017 / TC.JNP.017.001 — nama 256 karakter (ditolak)
test('violation type rejects a name with 256 characters', function () {
    $kesiswaan = jenisPelanggaranBvaKesiswaan();
    $nama256 = str_repeat('a', 256);

    $this->actingAs($kesiswaan)->post(route('kesiswaan.jenis-pelanggaran.store'), [
        'nama' => $nama256,
        'kategori' => 'ringan',
        'pengurangan_poin' => 10,
        'aktif' => '1',
    ])->assertSessionHasErrors('nama');
});
