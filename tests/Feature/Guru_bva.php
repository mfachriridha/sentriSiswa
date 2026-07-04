<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function guruBvaAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

// ── Boundary: nama length, min:3 / max:100 ────────────────────────────────

// TS.Guru.011 / TC.Guru.011.001 — nama with 2 characters (just below the minimum of 3, invalid)
test('admin cannot create a teacher with a 2 character name', function () {
    $this->actingAs(guruBvaAdmin())->post(route('admin.guru.store'), [
        'nama' => 'Ab',
        'nip' => '198601012020121101',
        'peran' => 'wali_kelas',
    ])->assertSessionHasErrors('nama');
});

// TS.Guru.012 / TC.Guru.012.001 — nama with exactly 3 characters (at the minimum, valid)
test('admin can create a teacher with a 3 character name', function () {
    $this->actingAs(guruBvaAdmin())->post(route('admin.guru.store'), [
        'nama' => 'Abi',
        'nip' => '198601012020121102',
        'peran' => 'wali_kelas',
    ])->assertRedirect(route('admin.guru.index'));
});

// TS.Guru.013 / TC.Guru.013.001 — nama with exactly 100 characters (at the maximum, valid)
test('admin can create a teacher with a 100 character name', function () {
    $nama100 = str_repeat('a', 100);

    $this->actingAs(guruBvaAdmin())->post(route('admin.guru.store'), [
        'nama' => $nama100,
        'nip' => '198601012020121103',
        'peran' => 'wali_kelas',
    ])->assertRedirect(route('admin.guru.index'));
});

// TS.Guru.014 / TC.Guru.014.001 — nama with 101 characters (just above the maximum, invalid)
test('admin cannot create a teacher with a 101 character name', function () {
    $nama101 = str_repeat('a', 101);

    $this->actingAs(guruBvaAdmin())->post(route('admin.guru.store'), [
        'nama' => $nama101,
        'nip' => '198601012020121104',
        'peran' => 'wali_kelas',
    ])->assertSessionHasErrors('nama');
});

// ── Boundary: nip length, max:30 ──────────────────────────────────────────

// TS.Guru.015 / TC.Guru.015.001 — nip with exactly 30 characters (at the maximum, valid)
test('admin can create a teacher with a 30 character nip', function () {
    $nip30 = str_repeat('9', 30);

    $this->actingAs(guruBvaAdmin())->post(route('admin.guru.store'), [
        'nama' => 'Guru Nip Tigapuluh',
        'nip' => $nip30,
        'peran' => 'wali_kelas',
    ])->assertRedirect(route('admin.guru.index'));
});

// TS.Guru.016 / TC.Guru.016.001 — nip with 31 characters (just above the maximum, invalid)
test('admin cannot create a teacher with a 31 character nip', function () {
    $nip31 = str_repeat('9', 31);

    $this->actingAs(guruBvaAdmin())->post(route('admin.guru.store'), [
        'nama' => 'Guru Nip Tigapuluhsatu',
        'nip' => $nip31,
        'peran' => 'wali_kelas',
    ])->assertSessionHasErrors('nip');
});

// ── Boundary: telepon length, min:10 / max:20 ─────────────────────────────

// TS.Guru.017 / TC.Guru.017.001 — telepon with 9 characters (just below the minimum of 10, invalid)
test('admin cannot create a teacher with a 9 character telepon', function () {
    $this->actingAs(guruBvaAdmin())->post(route('admin.guru.store'), [
        'nama' => 'Guru Telepon Sembilan',
        'nip' => '198601012020121110',
        'peran' => 'wali_kelas',
        'telepon' => '081234567',
    ])->assertSessionHasErrors('telepon');
});

// TS.Guru.018 / TC.Guru.018.001 — telepon with exactly 10 characters (at the minimum, valid)
test('admin can create a teacher with a 10 character telepon', function () {
    $this->actingAs(guruBvaAdmin())->post(route('admin.guru.store'), [
        'nama' => 'Guru Telepon Sepuluh',
        'nip' => '198601012020121111',
        'peran' => 'wali_kelas',
        'telepon' => '0812345678',
    ])->assertRedirect(route('admin.guru.index'));
});

// TS.Guru.019 / TC.Guru.019.001 — telepon with exactly 20 characters (at the maximum, valid)
test('admin can create a teacher with a 20 character telepon', function () {
    $telepon20 = str_repeat('0', 20);

    $this->actingAs(guruBvaAdmin())->post(route('admin.guru.store'), [
        'nama' => 'Guru Telepon Duapuluh',
        'nip' => '198601012020121112',
        'peran' => 'wali_kelas',
        'telepon' => $telepon20,
    ])->assertRedirect(route('admin.guru.index'));
});

// TS.Guru.020 / TC.Guru.020.001 — telepon with 21 characters (just above the maximum, invalid)
test('admin cannot create a teacher with a 21 character telepon', function () {
    $telepon21 = str_repeat('0', 21);

    $this->actingAs(guruBvaAdmin())->post(route('admin.guru.store'), [
        'nama' => 'Guru Telepon Duapuluhsatu',
        'nip' => '198601012020121113',
        'peran' => 'wali_kelas',
        'telepon' => $telepon21,
    ])->assertSessionHasErrors('telepon');
});
