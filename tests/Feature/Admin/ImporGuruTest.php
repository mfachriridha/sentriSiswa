<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('admin can access import guru page', function () {
    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.guru.impor'));

    $response->assertOk();
});

test('admin can download guru import template', function () {
    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.guru.impor.template'));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('admin can upload guru import file', function () {
    Storage::fake('local');

    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $file = UploadedFile::fake()->create('guru.xlsx', 100);

    $response = $this->actingAs($admin)->post(route('admin.guru.impor.unggah'), [
        'file' => $file,
    ]);

    $response->assertRedirect(route('admin.guru.impor.pratinjau'));
});

test('admin cannot upload invalid file for guru import', function () {
    $admin = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.guru.impor.unggah'), [
        'file' => 'not-a-file',
    ]);

    $response->assertSessionHasErrors('file');
});

test('non-admin cannot access import guru page', function () {
    $siswa = User::factory()->create([
        'peran' => User::PERAN_SISWA,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($siswa)->get(route('admin.guru.impor'));

    $response->assertForbidden();
});
