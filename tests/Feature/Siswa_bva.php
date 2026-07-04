<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function siswaBvaAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

// ── Boundary: nama length, min:3 / max:100 ────────────────────────────────

// TS.Siswa.011 / TC.Siswa.011.001 — nama with 2 characters (just below the minimum of 3, invalid)
test('admin cannot create a student with a 2 character name', function () {
    $this->actingAs(siswaBvaAdmin())->post(route('admin.siswa.store'), [
        'nama' => 'Ab',
        'nisn' => '1111111101',
        'nis' => '90001',
    ])->assertSessionHasErrors('nama');
});

// TS.Siswa.012 / TC.Siswa.012.001 — nama with exactly 3 characters (at the minimum, valid)
test('admin can create a student with a 3 character name', function () {
    $this->actingAs(siswaBvaAdmin())->post(route('admin.siswa.store'), [
        'nama' => 'Abi',
        'nisn' => '1111111102',
        'nis' => '90002',
    ])->assertRedirect(route('admin.siswa.index'));
});

// TS.Siswa.013 / TC.Siswa.013.001 — nama with exactly 100 characters (at the maximum, valid)
test('admin can create a student with a 100 character name', function () {
    $nama100 = str_repeat('a', 100);

    $this->actingAs(siswaBvaAdmin())->post(route('admin.siswa.store'), [
        'nama' => $nama100,
        'nisn' => '1111111103',
        'nis' => '90003',
    ])->assertRedirect(route('admin.siswa.index'));
});

// TS.Siswa.014 / TC.Siswa.014.001 — nama with 101 characters (just above the maximum, invalid)
test('admin cannot create a student with a 101 character name', function () {
    $nama101 = str_repeat('a', 101);

    $this->actingAs(siswaBvaAdmin())->post(route('admin.siswa.store'), [
        'nama' => $nama101,
        'nisn' => '1111111104',
        'nis' => '90004',
    ])->assertSessionHasErrors('nama');
});

// ── Boundary: nisn exact length, digits:10 ────────────────────────────────

// TS.Siswa.015 / TC.Siswa.015.001 — nisn with 9 digits (below the required 10, invalid)
test('admin cannot create a student with a 9 digit nisn', function () {
    $this->actingAs(siswaBvaAdmin())->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Nisn Sembilan',
        'nisn' => '123456789',
        'nis' => '90005',
    ])->assertSessionHasErrors('nisn');
});

// TS.Siswa.016 / TC.Siswa.016.001 — nisn with exactly 10 digits (valid)
test('admin can create a student with a 10 digit nisn', function () {
    $this->actingAs(siswaBvaAdmin())->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Nisn Sepuluh',
        'nisn' => '1234567895',
        'nis' => '90006',
    ])->assertRedirect(route('admin.siswa.index'));
});

// TS.Siswa.017 / TC.Siswa.017.001 — nisn with 11 digits (above the required 10, invalid)
test('admin cannot create a student with an 11 digit nisn', function () {
    $this->actingAs(siswaBvaAdmin())->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Nisn Sebelas',
        'nisn' => '12345678951',
        'nis' => '90007',
    ])->assertSessionHasErrors('nisn');
});

// ── Boundary: nis length, max:20 ──────────────────────────────────────────

// TS.Siswa.018 / TC.Siswa.018.001 — nis with exactly 20 characters (at the maximum, valid)
test('admin can create a student with a 20 character nis', function () {
    $nis20 = str_repeat('9', 20);

    $this->actingAs(siswaBvaAdmin())->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Nis Duapuluh',
        'nisn' => '1111111108',
        'nis' => $nis20,
    ])->assertRedirect(route('admin.siswa.index'));
});

// TS.Siswa.019 / TC.Siswa.019.001 — nis with 21 characters (just above the maximum, invalid)
test('admin cannot create a student with a 21 character nis', function () {
    $nis21 = str_repeat('9', 21);

    $this->actingAs(siswaBvaAdmin())->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Nis Duapuluhsatu',
        'nisn' => '1111111109',
        'nis' => $nis21,
    ])->assertSessionHasErrors('nis');
});

// ── Boundary: telepon length, min:10 / max:20 ─────────────────────────────

// TS.Siswa.020 / TC.Siswa.020.001 — telepon with 9 characters (just below the minimum of 10, invalid)
test('admin cannot create a student with a 9 character telepon', function () {
    $this->actingAs(siswaBvaAdmin())->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Telepon Sembilan',
        'nisn' => '1111111110',
        'nis' => '90010',
        'telepon' => '081234567',
    ])->assertSessionHasErrors('telepon');
});

// TS.Siswa.021 / TC.Siswa.021.001 — telepon with exactly 10 characters (at the minimum, valid)
test('admin can create a student with a 10 character telepon', function () {
    $this->actingAs(siswaBvaAdmin())->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Telepon Sepuluh',
        'nisn' => '1111111111',
        'nis' => '90011',
        'telepon' => '0812345678',
    ])->assertRedirect(route('admin.siswa.index'));
});

// TS.Siswa.022 / TC.Siswa.022.001 — telepon with exactly 20 characters (at the maximum, valid)
test('admin can create a student with a 20 character telepon', function () {
    $telepon20 = str_repeat('0', 20);

    $this->actingAs(siswaBvaAdmin())->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Telepon Duapuluh',
        'nisn' => '1111111112',
        'nis' => '90012',
        'telepon' => $telepon20,
    ])->assertRedirect(route('admin.siswa.index'));
});

// TS.Siswa.023 / TC.Siswa.023.001 — telepon with 21 characters (just above the maximum, invalid)
test('admin cannot create a student with a 21 character telepon', function () {
    $telepon21 = str_repeat('0', 21);

    $this->actingAs(siswaBvaAdmin())->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Telepon Duapuluhsatu',
        'nisn' => '1111111113',
        'nis' => '90013',
        'telepon' => $telepon21,
    ])->assertSessionHasErrors('telepon');
});
