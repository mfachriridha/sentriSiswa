<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('admin can access import siswa page', function () {
    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.siswa.impor'));

    $response->assertOk();
});

test('admin can download siswa import template', function () {
    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.siswa.impor.template'));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('admin can upload siswa import file', function () {
    Storage::fake('local');

    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $file = UploadedFile::fake()->create('siswa.xlsx', 100);

    $response = $this->actingAs($admin)->post(route('admin.siswa.impor.unggah'), [
        'file' => $file,
    ]);

    $response->assertRedirect(route('admin.siswa.impor.pratinjau'));
});

test('admin cannot upload invalid file for siswa import', function () {
    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.siswa.impor.unggah'), [
        'file' => 'not-a-file',
    ]);

    $response->assertSessionHasErrors('file');
});

test('non-admin cannot access import siswa page', function () {
    $siswa = User::factory()->create([
        'peran' => User::PERAN_SISWA,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($siswa)->get(route('admin.siswa.impor'));

    $response->assertForbidden();
});
