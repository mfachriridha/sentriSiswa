<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilGuru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

uses(RefreshDatabase::class);

function importGuruAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

function importGuruXlsxFile(array $headings, array $rows, string $filename = 'guru.xlsx'): UploadedFile
{
    $spreadsheet = new Spreadsheet;
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray($headings, null, 'A1');
    $sheet->fromArray($rows, null, 'A2');

    $path = sys_get_temp_dir().'/'.uniqid('import_guru_').'.xlsx';
    (new Xlsx($spreadsheet))->save($path);

    return new UploadedFile($path, $filename, null, null, true);
}

function importGuruUploadAndGetPath(Pengguna $admin, array $headings, array $rows): string
{
    test()->actingAs($admin)->post(route('admin.guru.impor.unggah'), [
        'file' => importGuruXlsxFile($headings, $rows),
    ])->assertRedirect(route('admin.guru.impor.pratinjau'));

    return session('import_guru_file_path');
}

// TS.IMG.001 / TC.IMG.001.001 — upload valid xlsx (new format) then import creates new teachers with the right peran (positive)
test('admin can upload a valid xlsx and import new teachers', function () {
    $admin = importGuruAdmin();
    $path = importGuruUploadAndGetPath($admin, ['Nama', 'NIP', 'Tipe', 'Kelas'], [
        ['Guru Impor Satu', '198601011000000001', 'BK', '10'],
    ]);

    $this->actingAs($admin)->get(route('admin.guru.impor.pratinjau'))
        ->assertSuccessful()
        ->assertSee('Guru Impor Satu');

    $this->actingAs($admin)->post(route('admin.guru.impor.store'), ['file_path' => $path])
        ->assertRedirect(route('admin.guru.index'));

    $teacher = Pengguna::where('nama', 'Guru Impor Satu')->firstOrFail();
    expect($teacher->peran)->toBe('bk');
    $this->assertDatabaseHas('profil_guru', ['pengguna_id' => $teacher->id, 'nip' => '198601011000000001']);
});

// TS.IMG.002 / TC.IMG.002.001 — upload a file that is not xlsx/xls (negative)
test('admin cannot upload a non-spreadsheet file for teacher import', function () {
    $admin = importGuruAdmin();

    $this->actingAs($admin)->post(route('admin.guru.impor.unggah'), [
        'file' => UploadedFile::fake()->create('guru.pdf', 10, 'application/pdf'),
    ])->assertSessionHasErrors('file');
});

// TS.IMG.003 / TC.IMG.003.001 — submit upload without any file (negative)
test('admin cannot submit teacher import without a file', function () {
    $admin = importGuruAdmin();

    $this->actingAs($admin)->post(route('admin.guru.impor.unggah'), [])
        ->assertSessionHasErrors('file');
});

// TS.IMG.004 / TC.IMG.004.001 — a row with an empty name is skipped and reported (negative)
test('teacher import skips a row with an empty name', function () {
    $admin = importGuruAdmin();
    $path = importGuruUploadAndGetPath($admin, ['Nama', 'NIP', 'Tipe', 'Kelas'], [
        ['', '198602022000000002', 'Wali Kelas', '10. 2'],
    ]);

    $this->actingAs($admin)->post(route('admin.guru.impor.store'), ['file_path' => $path])
        ->assertRedirect(route('admin.guru.index'));

    expect(Pengguna::whereIn('peran', ['wali_kelas', 'bk', 'kesiswaan'])->count())->toBe(0);
});

// TS.IMG.005 / TC.IMG.005.001 — a row with an empty nip is skipped and reported (negative)
test('teacher import skips a row with an empty nip', function () {
    $admin = importGuruAdmin();
    $path = importGuruUploadAndGetPath($admin, ['Nama', 'NIP', 'Tipe', 'Kelas'], [
        ['Guru Tanpa Nip', '', 'Wali Kelas', '10. 3'],
    ]);

    $this->actingAs($admin)->post(route('admin.guru.impor.store'), ['file_path' => $path])
        ->assertRedirect(route('admin.guru.index'));

    expect(Pengguna::whereIn('peran', ['wali_kelas', 'bk', 'kesiswaan'])->count())->toBe(0);
});

// TS.IMG.006 / TC.IMG.006.001 — a row whose nip already exists is treated as existing, not duplicated (positive)
test('teacher import treats an existing nip as existing, not a duplicate', function () {
    $admin = importGuruAdmin();
    $existing = Pengguna::factory()->homeroom()->create(['nama' => 'Guru Lama']);
    ProfilGuru::factory()->create(['pengguna_id' => $existing->id, 'nip' => '198603033000000003']);

    $path = importGuruUploadAndGetPath($admin, ['Nama', 'NIP', 'Tipe', 'Kelas'], [
        ['Guru Lama', '198603033000000003', 'Wali Kelas', '10. 4'],
    ]);

    $this->actingAs($admin)->post(route('admin.guru.impor.store'), ['file_path' => $path])
        ->assertRedirect(route('admin.guru.index'));

    expect(Pengguna::whereIn('peran', ['wali_kelas', 'bk', 'kesiswaan'])->count())->toBe(1);
});

// TS.IMG.007 / TC.IMG.007.001 — a wali kelas row assigns the teacher as the class's homeroom teacher (positive)
test('teacher import assigns a homeroom teacher to their class', function () {
    $admin = importGuruAdmin();
    $path = importGuruUploadAndGetPath($admin, ['Nama', 'NIP', 'Tipe', 'Kelas'], [
        ['Guru Wali Baru', '198604044000000004', 'Wali Kelas', '10. Wali'],
    ]);

    $this->actingAs($admin)->post(route('admin.guru.impor.store'), ['file_path' => $path])
        ->assertRedirect(route('admin.guru.index'));

    $teacher = Pengguna::where('nama', 'Guru Wali Baru')->firstOrFail();
    $this->assertDatabaseHas('kelas', ['nama' => '10. Wali', 'wali_kelas_id' => (string) $teacher->id]);
});

// TS.IMG.008 / TC.IMG.008.001 — the legacy "Walas" column format is still recognized and imported as wali_kelas (positive)
test('teacher import recognizes the legacy walas column format', function () {
    $admin = importGuruAdmin();
    $path = importGuruUploadAndGetPath($admin, ['Walas', 'NIP', 'Kelas'], [
        ['Guru Format Lama', '198605055000000005', '10. 5'],
    ]);

    $this->actingAs($admin)->post(route('admin.guru.impor.store'), ['file_path' => $path])
        ->assertRedirect(route('admin.guru.index'));

    $teacher = Pengguna::where('nama', 'Guru Format Lama')->firstOrFail();
    expect($teacher->peran)->toBe('wali_kelas');
});

// TS.IMG.009 / TC.IMG.009.001 — accessing the preview page without uploading a file first redirects back (negative)
test('teacher import preview redirects back when no file was uploaded first', function () {
    $admin = importGuruAdmin();

    $this->actingAs($admin)->get(route('admin.guru.impor.pratinjau'))
        ->assertRedirect(route('admin.guru.impor'));
});
