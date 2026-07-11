<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Lokasi Absen (Admin) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Menguji nilai tepat di batas yang diperbolehkan dan tepat di luarnya:
|   - Toleransi jarak      : 0 sampai 500 meter.
|   - Ukuran berkas area   : maksimal 5 MB.
|   - Jumlah titik area    : minimal 3 titik.
|
*/

// ── Batas toleransi jarak: 0 sampai 500 meter ─────────────────────────────

// TS.LKA.008 / TC.LKA.008.001 — Negative
test('toleransi jarak minus satu meter ditolak karena di bawah batas minimum', function () {
    adminLokasiAbsen();

    $this->from('/admin/pengaturan/lokasi-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen/tolerance', ['tolerance_meters' => -1])
        ->assertSee('Toleransi minimal 0 meter.');
});

// TS.LKA.008 / TC.LKA.008.002 — Positive
test('toleransi jarak nol meter diterima karena tepat di batas minimum', function () {
    adminLokasiAbsen();

    $this->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen/tolerance', ['tolerance_meters' => 0])
        ->assertSee('Toleransi jarak berhasil disimpan.');
});

// TS.LKA.009 / TC.LKA.009.001 — Positive
test('toleransi jarak lima ratus meter diterima karena tepat di batas maksimum', function () {
    adminLokasiAbsen();

    $this->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen/tolerance', ['tolerance_meters' => 500])
        ->assertSee('Toleransi jarak berhasil disimpan.');
});

// TS.LKA.009 / TC.LKA.009.002 — Negative
test('toleransi jarak lima ratus satu meter ditolak karena melebihi batas maksimum', function () {
    adminLokasiAbsen();

    $this->from('/admin/pengaturan/lokasi-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen/tolerance', ['tolerance_meters' => 501])
        ->assertSee('Toleransi maksimal 500 meter.');
});

// ── Batas jumlah titik area: minimal 3 titik ──────────────────────────────

// TS.LKA.010 / TC.LKA.010.001 — Negative
test('area dengan dua titik ditolak karena kurang dari jumlah titik minimum', function () {
    adminLokasiAbsen();

    $duaTitik = <<<'KML'
    <?xml version="1.0" encoding="UTF-8"?>
    <kml xmlns="http://www.opengis.net/kml/2.2">
      <Document><Placemark><Polygon><outerBoundaryIs><LinearRing>
        <coordinates>106.827,-6.175,0 106.828,-6.175,0</coordinates>
      </LinearRing></outerBoundaryIs></Polygon></Placemark></Document>
    </kml>
    KML;

    $this->from('/admin/pengaturan/lokasi-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen', [
            'kml_file' => UploadedFile::fake()->createWithContent('dua-titik.kml', $duaTitik),
        ])
        ->assertSee('Polygon harus memiliki minimal 3 titik koordinat unik.');
});

// TS.LKA.010 / TC.LKA.010.002 — Positive
test('area dengan tiga titik diterima karena tepat di jumlah titik minimum', function () {
    adminLokasiAbsen();

    $tigaTitik = <<<'KML'
    <?xml version="1.0" encoding="UTF-8"?>
    <kml xmlns="http://www.opengis.net/kml/2.2">
      <Document><Placemark><Polygon><outerBoundaryIs><LinearRing>
        <coordinates>106.827,-6.175,0 106.828,-6.175,0 106.828,-6.176,0 106.827,-6.175,0</coordinates>
      </LinearRing></outerBoundaryIs></Polygon></Placemark></Document>
    </kml>
    KML;

    $this->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen', [
            'kml_file' => UploadedFile::fake()->createWithContent('tiga-titik.kml', $tigaTitik),
        ])
        ->assertSee('Area absensi berhasil diimpor.');
});

// ── Batas ukuran berkas area: maksimal 5 MB ───────────────────────────────

// TS.LKA.011 / TC.LKA.011.001 — Negative
test('berkas area yang melebihi lima megabita ditolak karena di atas batas maksimum', function () {
    adminLokasiAbsen();

    $this->from('/admin/pengaturan/lokasi-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen', [
            'kml_file' => UploadedFile::fake()
                ->create('besar.kml', 5121, 'application/vnd.google-earth.kml+xml'),
        ])
        ->assertSee('File maksimal 5 MB.');
});
