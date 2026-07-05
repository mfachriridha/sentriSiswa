<?php

use App\Models\Pengaturan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

function lokasiAbsenAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

function lokasiAbsenValidKmlContent(): string
{
    return <<<'KML'
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2"><Document><Placemark><Polygon><outerBoundaryIs><LinearRing><coordinates>106.8272,-6.1751,0 106.8280,-6.1751,0 106.8280,-6.1760,0 106.8272,-6.1760,0 106.8272,-6.1751,0</coordinates></LinearRing></outerBoundaryIs></Polygon></Placemark></Document></kml>
KML;
}

function lokasiAbsenKmlFile(string $filename = 'area.kml', ?string $content = null): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'kml');
    file_put_contents($path, $content ?? lokasiAbsenValidKmlContent());

    return new UploadedFile($path, $filename, null, null, true);
}

// TS.LKA.001 / TC.LKA.001.001 — upload a valid KML polygon file (positive)
test('admin can upload a valid kml file', function () {
    $admin = lokasiAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.lokasi-absen.update'), [
        'kml_file' => lokasiAbsenKmlFile(),
    ])->assertRedirect(route('admin.pengaturan.lokasi-absen.index'));

    $geofence = json_decode(Pengaturan::get('attendance_geofence_data'), true);
    expect($geofence['coordinates'])->toHaveCount(4);
});

// TS.LKA.002 / TC.LKA.002.001 — upload a file that is not a valid KML format (negative)
test('admin cannot upload a file with an invalid kml format', function () {
    $admin = lokasiAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.lokasi-absen.update'), [
        'kml_file' => lokasiAbsenKmlFile('area.kml', 'ini bukan file kml sama sekali, cuma teks biasa'),
    ])->assertSessionHasErrors('kml_file');
});

// TS.LKA.003 / TC.LKA.003.001 — submit the form without a file at all (negative)
test('admin cannot save attendance location without a kml file', function () {
    $admin = lokasiAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.lokasi-absen.update'), [])
        ->assertSessionHasErrors('kml_file');
});

// TS.LKA.004 / TC.LKA.004.001 — set a tolerance value within the valid 0-500 range (positive)
test('admin can save a tolerance within the valid range', function () {
    $admin = lokasiAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.lokasi-absen.tolerance'), [
        'tolerance_meters' => '50',
    ])->assertRedirect(route('admin.pengaturan.lokasi-absen.index'));

    expect(Pengaturan::get('attendance_tolerance_meters'))->toBe('50');
});

// TS.LKA.005 / TC.LKA.005.001 — delete the saved location clears the geofence and resets tolerance (positive)
test('admin can delete the saved attendance location', function () {
    $admin = lokasiAbsenAdmin();
    Pengaturan::set('attendance_geofence_data', json_encode(['coordinates' => [['lat' => 1, 'lng' => 1]]]));
    Pengaturan::set('attendance_tolerance_meters', '80');

    $this->actingAs($admin)->delete(route('admin.pengaturan.lokasi-absen.destroy'))
        ->assertRedirect(route('admin.pengaturan.lokasi-absen.index'));

    expect(Pengaturan::get('attendance_geofence_data'))->toBe('');
    expect(Pengaturan::get('attendance_tolerance_meters'))->toBe('0');
});
