<?php

use App\Models\Pengaturan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

function lokasiAbsenSttAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

function lokasiAbsenSttKmlFile(): UploadedFile
{
    $content = '<?xml version="1.0" encoding="UTF-8"?><kml xmlns="http://www.opengis.net/kml/2.2"><Document><Placemark><Polygon><outerBoundaryIs><LinearRing><coordinates>106.8272,-6.1751,0 106.8280,-6.1751,0 106.8280,-6.1760,0 106.8272,-6.1760,0 106.8272,-6.1751,0</coordinates></LinearRing></outerBoundaryIs></Polygon></Placemark></Document></kml>';
    $path = tempnam(sys_get_temp_dir(), 'kml');
    file_put_contents($path, $content);

    return new UploadedFile($path, 'area.kml', null, null, true);
}

function lokasiAbsenIsKosong(): bool
{
    return blank(Pengaturan::get('attendance_geofence_data'));
}

// State machine under test: Kosong ⇄ Tersimpan (attendance_geofence_data empty vs filled)

// TS.LokasiAbsen.012 / TC.LokasiAbsen.012.001 — Kosong → upload valid KML → Tersimpan (positive)
test('lokasi absen transitions from kosong to tersimpan after a valid upload', function () {
    $admin = lokasiAbsenSttAdmin();
    expect(lokasiAbsenIsKosong())->toBeTrue();

    $this->actingAs($admin)->put(route('admin.settings.attendance-location.update'), [
        'kml_file' => lokasiAbsenSttKmlFile(),
    ])->assertRedirect(route('admin.settings.attendance-location.index'));

    expect(lokasiAbsenIsKosong())->toBeFalse();
});

// TS.LokasiAbsen.013 / TC.LokasiAbsen.013.001 — Tersimpan → upload new KML → stays Tersimpan, data replaced (positive)
test('lokasi absen stays tersimpan and replaces data when uploading again', function () {
    $admin = lokasiAbsenSttAdmin();
    Pengaturan::set('attendance_geofence_data', json_encode(['coordinates' => [['lat' => 1, 'lng' => 1], ['lat' => 2, 'lng' => 2], ['lat' => 3, 'lng' => 3]]]));
    $oldData = Pengaturan::get('attendance_geofence_data');

    $this->actingAs($admin)->put(route('admin.settings.attendance-location.update'), [
        'kml_file' => lokasiAbsenSttKmlFile(),
    ])->assertRedirect(route('admin.settings.attendance-location.index'));

    expect(lokasiAbsenIsKosong())->toBeFalse();
    expect(Pengaturan::get('attendance_geofence_data'))->not->toBe($oldData);
});

// TS.LokasiAbsen.014 / TC.LokasiAbsen.014.001 — Tersimpan → hapus → Kosong (positive)
test('lokasi absen transitions from tersimpan to kosong after delete', function () {
    $admin = lokasiAbsenSttAdmin();
    Pengaturan::set('attendance_geofence_data', json_encode(['coordinates' => [['lat' => 1, 'lng' => 1], ['lat' => 2, 'lng' => 2], ['lat' => 3, 'lng' => 3]]]));
    Pengaturan::set('attendance_tolerance_meters', '80');
    expect(lokasiAbsenIsKosong())->toBeFalse();

    $this->actingAs($admin)->delete(route('admin.settings.attendance-location.destroy'))
        ->assertRedirect(route('admin.settings.attendance-location.index'));

    expect(lokasiAbsenIsKosong())->toBeTrue();
    expect(Pengaturan::get('attendance_tolerance_meters'))->toBe('0');
});

// TS.LokasiAbsen.015 / TC.LokasiAbsen.015.001 — Kosong → set tolerance without a geofence first → stays Kosong, tolerance still saved (positive, documented quirk)
test('lokasi absen stays kosong but still saves tolerance when set before any upload', function () {
    $admin = lokasiAbsenSttAdmin();
    expect(lokasiAbsenIsKosong())->toBeTrue();

    $this->actingAs($admin)->put(route('admin.settings.attendance-location.tolerance'), [
        'tolerance_meters' => '25',
    ])->assertRedirect(route('admin.settings.attendance-location.index'));

    expect(lokasiAbsenIsKosong())->toBeTrue();
    expect(Pengaturan::get('attendance_tolerance_meters'))->toBe('25');
});

// TS.LokasiAbsen.016 / TC.LokasiAbsen.016.001 — Kosong → hapus (belum ada apa-apa) → stays Kosong, safe no-op (positive)
test('lokasi absen stays kosong and is a safe no-op when deleting with nothing saved', function () {
    $admin = lokasiAbsenSttAdmin();
    expect(lokasiAbsenIsKosong())->toBeTrue();

    $this->actingAs($admin)->delete(route('admin.settings.attendance-location.destroy'))
        ->assertRedirect(route('admin.settings.attendance-location.index'));

    expect(lokasiAbsenIsKosong())->toBeTrue();
});
