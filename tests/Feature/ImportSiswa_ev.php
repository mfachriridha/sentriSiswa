<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

uses(RefreshDatabase::class);

function importSiswaAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

function importSiswaXlsxFile(array $rows, string $filename = 'siswa.xlsx'): UploadedFile
{
    $spreadsheet = new Spreadsheet;
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray(['Nama', 'NISN', 'NIS', 'Kelas'], null, 'A1');
    $sheet->fromArray($rows, null, 'A2');

    $path = sys_get_temp_dir().'/'.uniqid('import_siswa_').'.xlsx';
    (new Xlsx($spreadsheet))->save($path);

    return new UploadedFile($path, $filename, null, null, true);
}

function importSiswaUploadAndGetPath(Pengguna $admin, array $rows): string
{
    test()->actingAs($admin)->post(route('admin.siswa.impor.unggah'), [
        'file' => importSiswaXlsxFile($rows),
    ])->assertRedirect(route('admin.siswa.impor.pratinjau'));

    return session('import_student_file_path');
}

// TS.ImportSiswa.001 / TC.ImportSiswa.001.001 — upload valid xlsx then import creates new students (positive)
test('admin can upload a valid xlsx and import new students', function () {
    $admin = importSiswaAdmin();
    $path = importSiswaUploadAndGetPath($admin, [
        ['Ahmad Fauzi', '0011122233', '11001', '10. 1'],
    ]);

    $this->actingAs($admin)->get(route('admin.siswa.impor.pratinjau'))
        ->assertSuccessful()
        ->assertSee('Ahmad Fauzi');

    $this->actingAs($admin)->post(route('admin.siswa.impor.store'), [
        'file_path' => $path,
    ])->assertRedirect(route('admin.siswa.index'));

    $student = Pengguna::where('nama', 'Ahmad Fauzi')->firstOrFail();
    $this->assertDatabaseHas('profil_siswa', ['pengguna_id' => $student->id, 'nisn' => '0011122233']);
});

// TS.ImportSiswa.002 / TC.ImportSiswa.002.001 — upload a file that is not xlsx/xls (negative)
test('admin cannot upload a non-spreadsheet file for student import', function () {
    $admin = importSiswaAdmin();

    $this->actingAs($admin)->post(route('admin.siswa.impor.unggah'), [
        'file' => UploadedFile::fake()->create('siswa.pdf', 10, 'application/pdf'),
    ])->assertSessionHasErrors('file');
});

// TS.ImportSiswa.003 / TC.ImportSiswa.003.001 — submit upload without any file (negative)
test('admin cannot submit student import without a file', function () {
    $admin = importSiswaAdmin();

    $this->actingAs($admin)->post(route('admin.siswa.impor.unggah'), [])
        ->assertSessionHasErrors('file');
});

// TS.ImportSiswa.004 / TC.ImportSiswa.004.001 — a row with an empty name is skipped and reported (negative)
test('student import skips a row with an empty name', function () {
    $admin = importSiswaAdmin();
    $path = importSiswaUploadAndGetPath($admin, [
        ['', '0022233344', '11002', '10. 1'],
    ]);

    $this->actingAs($admin)->post(route('admin.siswa.impor.store'), ['file_path' => $path])
        ->assertRedirect(route('admin.siswa.index'));

    expect(Pengguna::where('peran', 'siswa')->count())->toBe(0);
});

// TS.ImportSiswa.005 / TC.ImportSiswa.005.001 — a row with an empty nisn is skipped and reported (negative)
test('student import skips a row with an empty nisn', function () {
    $admin = importSiswaAdmin();
    $path = importSiswaUploadAndGetPath($admin, [
        ['Siswa Tanpa Nisn', '', '11003', '10. 1'],
    ]);

    $this->actingAs($admin)->post(route('admin.siswa.impor.store'), ['file_path' => $path])
        ->assertRedirect(route('admin.siswa.index'));

    expect(Pengguna::where('peran', 'siswa')->count())->toBe(0);
});

// TS.ImportSiswa.006 / TC.ImportSiswa.006.001 — a row with an empty nis is skipped and reported (negative)
test('student import skips a row with an empty nis', function () {
    $admin = importSiswaAdmin();
    $path = importSiswaUploadAndGetPath($admin, [
        ['Siswa Tanpa Nis', '0033344455', '', '10. 1'],
    ]);

    $this->actingAs($admin)->post(route('admin.siswa.impor.store'), ['file_path' => $path])
        ->assertRedirect(route('admin.siswa.index'));

    expect(Pengguna::where('peran', 'siswa')->count())->toBe(0);
});

// TS.ImportSiswa.007 / TC.ImportSiswa.007.001 — a row whose nisn already exists updates the existing profile instead of duplicating (positive)
test('student import treats an existing nisn as an update, not a duplicate', function () {
    $admin = importSiswaAdmin();
    $existing = Pengguna::factory()->student()->create(['nama' => 'Siswa Lama']);
    ProfilSiswa::factory()->create(['pengguna_id' => $existing->id, 'nisn' => '0044455566', 'nis' => '20000']);

    $path = importSiswaUploadAndGetPath($admin, [
        ['Siswa Lama', '0044455566', '20999', '10. 1'],
    ]);

    $this->actingAs($admin)->post(route('admin.siswa.impor.store'), ['file_path' => $path])
        ->assertRedirect(route('admin.siswa.index'));

    expect(Pengguna::where('peran', 'siswa')->count())->toBe(1);
    expect($existing->fresh()->profilSiswa->nis)->toBe('20999');
});

// TS.ImportSiswa.008 / TC.ImportSiswa.008.001 — a row referencing a class that does not exist yet creates it automatically (positive)
test('student import automatically creates a class that does not exist yet', function () {
    $admin = importSiswaAdmin();
    $path = importSiswaUploadAndGetPath($admin, [
        ['Siswa Kelas Baru', '0055566677', '11008', '10. Baru'],
    ]);

    $this->actingAs($admin)->post(route('admin.siswa.impor.store'), ['file_path' => $path])
        ->assertRedirect(route('admin.siswa.index'));

    $this->assertDatabaseHas('kelas', ['nama' => '10. Baru', 'tingkat' => '10']);
});

// TS.ImportSiswa.009 / TC.ImportSiswa.009.001 — accessing the preview page without uploading a file first redirects back (negative)
test('student import preview redirects back when no file was uploaded first', function () {
    $admin = importSiswaAdmin();

    $this->actingAs($admin)->get(route('admin.siswa.impor.pratinjau'))
        ->assertRedirect(route('admin.siswa.impor'));
});
