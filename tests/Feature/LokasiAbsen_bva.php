<?php

use App\Models\Pengaturan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

function lokasiAbsenBvaAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

function lokasiAbsenBvaKmlContentOfSize(int $bytes): string
{
    $base = '<?xml version="1.0" encoding="UTF-8"?><kml xmlns="http://www.opengis.net/kml/2.2"><Document><Placemark><Polygon><outerBoundaryIs><LinearRing><coordinates>106.8272,-6.1751,0 106.8280,-6.1751,0 106.8280,-6.1760,0 106.8272,-6.1760,0 106.8272,-6.1751,0</coordinates></LinearRing></outerBoundaryIs></Polygon></Placemark></Document></kml>';

    $padLength = max(0, $bytes - strlen($base) - 7);
    $comment = '<!--'.str_repeat('x', $padLength).'-->';

    return str_replace('?>', '?>'.$comment, $base);
}

function lokasiAbsenBvaKmlFile(int $bytes): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'kml');
    file_put_contents($path, lokasiAbsenBvaKmlContentOfSize($bytes));

    return new UploadedFile($path, 'area.kml', null, null, true);
}

// ── Boundary: kml_file size, max:5120 KB ──────────────────────────────────

// TS.LKA.006 / TC.LKA.006.001 — file size exactly 5120 KB (at the maximum, valid)
test('admin can upload a kml file at exactly the maximum size', function () {
    $admin = lokasiAbsenBvaAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.lokasi-absen.update'), [
        'kml_file' => lokasiAbsenBvaKmlFile(5120 * 1024),
    ])->assertRedirect(route('admin.pengaturan.lokasi-absen.index'));
});

// TS.LKA.007 / TC.LKA.007.001 — file size 1 byte above the 5120 KB maximum (invalid)
test('admin cannot upload a kml file above the maximum size', function () {
    $admin = lokasiAbsenBvaAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.lokasi-absen.update'), [
        'kml_file' => lokasiAbsenBvaKmlFile(5120 * 1024 + 1),
    ])->assertSessionHasErrors('kml_file');
});

// ── Boundary: tolerance_meters, min:0 / max:500 ────────────────────────────

// TS.LKA.008 / TC.LKA.008.001 — tolerance of -1 (just below the minimum of 0, invalid)
test('admin cannot save a tolerance of -1', function () {
    $admin = lokasiAbsenBvaAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.lokasi-absen.tolerance'), [
        'tolerance_meters' => '-1',
    ])->assertSessionHasErrors('tolerance_meters');
});

// TS.LKA.009 / TC.LKA.009.001 — tolerance of exactly 0 (at the minimum, valid)
test('admin can save a tolerance of exactly 0', function () {
    $admin = lokasiAbsenBvaAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.lokasi-absen.tolerance'), [
        'tolerance_meters' => '0',
    ])->assertRedirect(route('admin.pengaturan.lokasi-absen.index'));

    expect(Pengaturan::get('attendance_tolerance_meters'))->toBe('0');
});

// TS.LKA.010 / TC.LKA.010.001 — tolerance of exactly 500 (at the maximum, valid)
test('admin can save a tolerance of exactly 500', function () {
    $admin = lokasiAbsenBvaAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.lokasi-absen.tolerance'), [
        'tolerance_meters' => '500',
    ])->assertRedirect(route('admin.pengaturan.lokasi-absen.index'));

    expect(Pengaturan::get('attendance_tolerance_meters'))->toBe('500');
});

// TS.LKA.011 / TC.LKA.011.001 — tolerance of 501 (just above the maximum, invalid)
test('admin cannot save a tolerance of 501', function () {
    $admin = lokasiAbsenBvaAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.lokasi-absen.tolerance'), [
        'tolerance_meters' => '501',
    ])->assertSessionHasErrors('tolerance_meters');
});
