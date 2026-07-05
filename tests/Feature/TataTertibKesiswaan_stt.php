<?php

use App\Models\Pengguna;
use App\Models\TataTertib;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function tataTertibSttActor(): Pengguna
{
    $studentAffairs = Pengguna::factory()->studentAffairs()->create(['status' => 'registered']);
    $studentAffairs->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'student_affairs',
    ]);

    return $studentAffairs;
}

// State: Draft <-> Published — hanya boleh 1 tata tertib published dalam satu waktu.

// TS.TTK.011 / TC.TTK.011.001 — upload baru dengan dipublikasikan=true langsung published dan meng-unpublish yang lain
test('uploading a new rule as published auto-unpublishes the previously published rule', function () {
    Storage::fake('public');
    $kesiswaan = tataTertibSttActor();
    $oldPublished = TataTertib::create([
        'judul' => 'Tata Tertib Lama',
        'path_file' => 'school-rules/lama.pdf',
        'dipublikasikan' => true,
        'diunggah_oleh_id' => $kesiswaan->id,
    ]);

    $this->actingAs($kesiswaan)->post(route('kesiswaan.tata-tertib.store'), [
        'judul' => 'Tata Tertib Baru',
        'file_pdf' => UploadedFile::fake()->create('baru.pdf', 64, 'application/pdf'),
        'dipublikasikan' => '1',
    ])->assertRedirect(route('kesiswaan.tata-tertib.index'));

    expect($oldPublished->fresh()->dipublikasikan)->toBeFalse();
    $newRule = TataTertib::where('judul', 'Tata Tertib Baru')->firstOrFail();
    expect($newRule->dipublikasikan)->toBeTrue();
});

// TS.TTK.012 / TC.TTK.012.001 — publish salah satu draft membuatnya published dan meng-unpublish yang lain
test('publishing a draft rule makes it published and unpublishes the other one', function () {
    $kesiswaan = tataTertibSttActor();
    $currentlyPublished = TataTertib::create([
        'judul' => 'Tata Tertib Sedang Aktif',
        'path_file' => 'school-rules/aktif.pdf',
        'dipublikasikan' => true,
        'diunggah_oleh_id' => $kesiswaan->id,
    ]);
    $draft = TataTertib::create([
        'judul' => 'Tata Tertib Draft',
        'path_file' => 'school-rules/draft.pdf',
        'dipublikasikan' => false,
        'diunggah_oleh_id' => $kesiswaan->id,
    ]);

    $this->actingAs($kesiswaan)->put(route('kesiswaan.tata-tertib.publish', $draft))
        ->assertRedirect(route('kesiswaan.tata-tertib.index'));

    expect($draft->fresh()->dipublikasikan)->toBeTrue();
    expect($currentlyPublished->fresh()->dipublikasikan)->toBeFalse();
});

// TS.TTK.013 / TC.TTK.013.001 — unpublish yang published balik jadi draft
test('unpublishing a published rule reverts it back to draft', function () {
    $kesiswaan = tataTertibSttActor();
    $published = TataTertib::create([
        'judul' => 'Tata Tertib Akan Dinonaktifkan',
        'path_file' => 'school-rules/nonaktif.pdf',
        'dipublikasikan' => true,
        'diunggah_oleh_id' => $kesiswaan->id,
    ]);

    $this->actingAs($kesiswaan)->put(route('kesiswaan.tata-tertib.unpublish', $published))
        ->assertRedirect(route('kesiswaan.tata-tertib.index'));

    expect($published->fresh()->dipublikasikan)->toBeFalse();
});

// TS.TTK.014 / TC.TTK.014.001 — upload baru dengan dipublikasikan=false tetap draft, published lama gak terganggu
test('uploading a new rule as draft leaves the existing published rule untouched', function () {
    Storage::fake('public');
    $kesiswaan = tataTertibSttActor();
    $existingPublished = TataTertib::create([
        'judul' => 'Tata Tertib Tetap Aktif',
        'path_file' => 'school-rules/tetap-aktif.pdf',
        'dipublikasikan' => true,
        'diunggah_oleh_id' => $kesiswaan->id,
    ]);

    $this->actingAs($kesiswaan)->post(route('kesiswaan.tata-tertib.store'), [
        'judul' => 'Tata Tertib Draft Baru',
        'file_pdf' => UploadedFile::fake()->create('draft-baru.pdf', 64, 'application/pdf'),
        'dipublikasikan' => '0',
    ])->assertRedirect(route('kesiswaan.tata-tertib.index'));

    expect($existingPublished->fresh()->dipublikasikan)->toBeTrue();
    $newRule = TataTertib::where('judul', 'Tata Tertib Draft Baru')->firstOrFail();
    expect($newRule->dipublikasikan)->toBeFalse();
});
