<?php

use App\Models\Pengguna;
use App\Models\TataTertib;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function tataTertibKesiswaanActor(): Pengguna
{
    $studentAffairs = Pengguna::factory()->studentAffairs()->create(['status' => 'registered']);
    $studentAffairs->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'kesiswaan',
    ]);

    return $studentAffairs;
}

// TS.TTK.001 / TC.TTK.001.001 — upload PDF baru berhasil (positive)
test('student affairs can upload a new school rule pdf', function () {
    Storage::fake('public');
    $kesiswaan = tataTertibKesiswaanActor();

    $this->actingAs($kesiswaan)->post(route('kesiswaan.tata-tertib.store'), [
        'judul' => 'Tata Tertib Kedisiplinan 2026',
        'file_pdf' => UploadedFile::fake()->create('tertib.pdf', 128, 'application/pdf'),
        'dipublikasikan' => '0',
    ])->assertRedirect(route('kesiswaan.tata-tertib.index'));

    $rule = TataTertib::where('judul', 'Tata Tertib Kedisiplinan 2026')->firstOrFail();
    Storage::disk('public')->assertExists($rule->path_file);
});

// TS.TTK.002 / TC.TTK.002.001 — upload tanpa judul ditolak (negative)
test('student affairs cannot upload a school rule pdf without a title', function () {
    Storage::fake('public');
    $kesiswaan = tataTertibKesiswaanActor();

    $this->actingAs($kesiswaan)->post(route('kesiswaan.tata-tertib.store'), [
        'judul' => '',
        'file_pdf' => UploadedFile::fake()->create('tertib.pdf', 128, 'application/pdf'),
    ])->assertSessionHasErrors('judul');
});

// TS.TTK.003 / TC.TTK.003.001 — upload file bukan PDF ditolak (negative)
test('student affairs cannot upload a non-pdf file as a school rule', function () {
    Storage::fake('public');
    $kesiswaan = tataTertibKesiswaanActor();

    $this->actingAs($kesiswaan)->post(route('kesiswaan.tata-tertib.store'), [
        'judul' => 'Tata Tertib Salah Format',
        'file_pdf' => UploadedFile::fake()->create('tertib.docx', 128, 'application/msword'),
    ])->assertSessionHasErrors('file_pdf');
});

// TS.TTK.004 / TC.TTK.004.001 — upload tanpa file ditolak (negative)
test('student affairs cannot upload a school rule without a file', function () {
    $kesiswaan = tataTertibKesiswaanActor();

    $this->actingAs($kesiswaan)->post(route('kesiswaan.tata-tertib.store'), [
        'judul' => 'Tata Tertib Tanpa File',
    ])->assertSessionHasErrors('file_pdf');
});

// TS.TTK.005 / TC.TTK.005.001 — hapus tata tertib berhasil termasuk file storage (positive)
test('student affairs can delete a school rule including its stored file', function () {
    Storage::fake('public');
    $kesiswaan = tataTertibKesiswaanActor();
    $path = UploadedFile::fake()->create('hapus.pdf', 64, 'application/pdf')->store('school-rules', 'public');
    $rule = TataTertib::create([
        'judul' => 'Tata Tertib Akan Dihapus',
        'path_file' => $path,
        'dipublikasikan' => false,
        'diunggah_oleh_id' => $kesiswaan->id,
    ]);

    $this->actingAs($kesiswaan)->delete(route('kesiswaan.tata-tertib.destroy', $rule))
        ->assertRedirect(route('kesiswaan.tata-tertib.index'));

    $this->assertDatabaseMissing('tata_tertib', ['id' => $rule->id]);
    Storage::disk('public')->assertMissing($path);
});

// TS.TTK.006 / TC.TTK.006.001 — index nampilin daftar tata tertib (positive)
test('school rule index displays the list of uploaded rules', function () {
    $kesiswaan = tataTertibKesiswaanActor();
    TataTertib::create([
        'judul' => 'Tata Tertib Terdaftar',
        'path_file' => 'school-rules/contoh.pdf',
        'dipublikasikan' => false,
        'diunggah_oleh_id' => $kesiswaan->id,
    ]);

    $this->actingAs($kesiswaan)->get(route('kesiswaan.tata-tertib.index'))
        ->assertSuccessful()
        ->assertSee('Tata Tertib Terdaftar');
});
