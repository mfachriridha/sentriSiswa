<?php

use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can access waktu absen settings page', function () {
    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.pengaturan.waktu-absen'));

    $response->assertOk();
});

test('admin can update waktu absen settings', function () {
    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($admin)->put(route('admin.pengaturan.waktu-absen.simpan'), [
        'jam_mulai' => '06',
        'menit_mulai' => '30',
        'jam_selesai' => '07',
        'menit_selesai' => '00',
        'toleransi_terlambat' => '15',
    ]);

    $response->assertRedirect(route('admin.pengaturan.waktu-absen'));
    
    expect(Pengaturan::ambil('waktu_mulai'))->toBe('06:30');
    expect(Pengaturan::ambil('waktu_selesai'))->toBe('07:00');
    expect(Pengaturan::ambil('toleransi_terlambat'))->toBe('15');
    expect(Pengaturan::ambil('waktu_terlambat'))->toBe('06:45');
});

test('waktu selesai must be after waktu mulai', function () {
    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($admin)->put(route('admin.pengaturan.waktu-absen.simpan'), [
        'jam_mulai' => '07',
        'menit_mulai' => '00',
        'jam_selesai' => '06',
        'menit_selesai' => '30',
        'toleransi_terlambat' => '15',
    ]);

    $response->assertSessionHasErrors('jam_selesai');
});

test('toleransi terlambat cannot exceed attendance duration', function () {
    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    // Durasi absen: 06:30 - 07:00 = 30 menit
    // Toleransi: 45 menit (lebih besar dari durasi)
    $response = $this->actingAs($admin)->put(route('admin.pengaturan.waktu-absen.simpan'), [
        'jam_mulai' => '06',
        'menit_mulai' => '30',
        'jam_selesai' => '07',
        'menit_selesai' => '00',
        'toleransi_terlambat' => '45',
    ]);

    $response->assertSessionHasErrors('toleransi_terlambat');
});

test('admin can access lokasi absen settings page', function () {
    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.pengaturan.lokasi-absen'));

    $response->assertOk();
});

test('admin can delete lokasi absen settings', function () {
    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    Pengaturan::simpan('geofence_data', '{"coordinates":[]}');
    Pengaturan::simpan('toleransi_meter', '50');

    $response = $this->actingAs($admin)->delete(route('admin.pengaturan.lokasi-absen.hapus'));

    $response->assertRedirect(route('admin.pengaturan.lokasi-absen'));
    
    expect(Pengaturan::ambil('geofence_data'))->toBe('');
    expect(Pengaturan::ambil('toleransi_meter'))->toBe('0');
});

test('non-admin cannot access pengaturan pages', function () {
    $siswa = User::factory()->create([
        'peran' => User::PERAN_SISWA,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($siswa)->get(route('admin.pengaturan.waktu-absen'));

    $response->assertForbidden();
});
