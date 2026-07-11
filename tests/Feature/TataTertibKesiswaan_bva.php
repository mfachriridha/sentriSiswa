<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function tataTertibBvaActor(): Pengguna
{
    $studentAffairs = Pengguna::factory()->studentAffairs()->create(['status' => 'registered']);
    $studentAffairs->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'kesiswaan',
    ]);

    return $studentAffairs;
}

// ── Boundary: judul max:200 ─────────────────────────────────────────────────

// TS.TTK.007 / TC.TTK.007.001 — judul tepat 200 karakter (diperbolehkan)
test('school rule accepts a title with exactly 200 characters', function () {
    Storage::fake('public');
    $kesiswaan = tataTertibBvaActor();
    $judul200 = str_repeat('a', 200);

    $this->actingAs($kesiswaan)->post(route('kesiswaan.tata-tertib.store'), [
        'judul' => $judul200,
        'file_pdf' => UploadedFile::fake()->create('tertib200.pdf', 64, 'application/pdf'),
    ])->assertRedirect(route('kesiswaan.tata-tertib.index'));

    $this->assertDatabaseHas('tata_tertib', ['judul' => $judul200]);
});

// TS.TTK.008 / TC.TTK.008.001 — judul 201 karakter (ditolak)
test('school rule rejects a title with 201 characters', function () {
    Storage::fake('public');
    $kesiswaan = tataTertibBvaActor();
    $judul201 = str_repeat('a', 201);

    $this->actingAs($kesiswaan)->post(route('kesiswaan.tata-tertib.store'), [
        'judul' => $judul201,
        'file_pdf' => UploadedFile::fake()->create('tertib201.pdf', 64, 'application/pdf'),
    ])->assertSessionHasErrors('judul');
});

// ── Boundary: file_pdf max:10240 KB ─────────────────────────────────────────

// TS.TTK.009 / TC.TTK.009.001 — file tepat 10240 KB (diperbolehkan)
test('school rule accepts a pdf file with exactly 10240 KB', function () {
    Storage::fake('public');
    $kesiswaan = tataTertibBvaActor();

    $this->actingAs($kesiswaan)->post(route('kesiswaan.tata-tertib.store'), [
        'judul' => 'Tata Tertib File Batas Atas',
        'file_pdf' => UploadedFile::fake()->create('besar10240.pdf', 10240, 'application/pdf'),
    ])->assertRedirect(route('kesiswaan.tata-tertib.index'));

    $this->assertDatabaseHas('tata_tertib', ['judul' => 'Tata Tertib File Batas Atas']);
});

// TS.TTK.010 / TC.TTK.010.001 — file 10241 KB (ditolak)
test('school rule rejects a pdf file with 10241 KB', function () {
    Storage::fake('public');
    $kesiswaan = tataTertibBvaActor();

    $this->actingAs($kesiswaan)->post(route('kesiswaan.tata-tertib.store'), [
        'judul' => 'Tata Tertib File Kelebihan',
        'file_pdf' => UploadedFile::fake()->create('besar10241.pdf', 10241, 'application/pdf'),
    ])->assertSessionHasErrors('file_pdf');
});
