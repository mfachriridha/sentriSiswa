<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

function biodataFotoAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

function biodataFotoSiswa(): Pengguna
{
    $kelas = Kelas::create(['nama' => '10. 1', 'tingkat' => '10']);
    $siswa = Pengguna::factory()->create(['peran' => 'siswa', 'status' => 'registered']);
    ProfilSiswa::factory()->create(['pengguna_id' => $siswa->id, 'kelas_id' => $kelas->id]);

    return $siswa;
}

// TS.BIO.001 / TC.BIO.001.001 — admin uploads a valid small JPG photo for a student's biodata (positive)
test('admin can upload a valid photo for student biodata', function () {
    $admin = biodataFotoAdmin();
    $siswa = biodataFotoSiswa();

    $photo = UploadedFile::fake()->image('selfie.jpg', 300, 400)->size(50);

    $response = $this->actingAs($admin)->post(route('admin.siswa.foto', $siswa), [
        'photo' => $photo,
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure(['url']);

    $siswa->refresh()->load('profilSiswa');
    expect($siswa->profilSiswa->foto)->not->toBeNull();

    $absolutePath = storage_path('app/public/'.$siswa->profilSiswa->foto);
    expect(file_exists($absolutePath))->toBeTrue();

    unlink($absolutePath);
});
