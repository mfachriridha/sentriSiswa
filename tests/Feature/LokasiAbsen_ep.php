<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Lokasi Absen (Admin) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengatur area
| absensi seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Area absensi digambar di Google My Maps lalu diunduh sebagai berkas KML.
| Admin mengunggah berkas itu, dan boleh menambahkan toleransi jarak agar
| siswa yang berada sedikit di luar garis area tetap bisa absen.
|
*/

function adminLokasiAbsen(): Pengguna
{
    $admin = Pengguna::factory()->admin()->create([
        'email' => 'admin.lokasi@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($admin);

    return $admin;
}

/** Berkas KML berisi area sekolah, seperti yang diunduh dari Google My Maps. */
function berkasAreaSekolah(string $namaBerkas = 'area-sekolah.kml'): UploadedFile
{
    $isi = <<<'KML'
    <?xml version="1.0" encoding="UTF-8"?>
    <kml xmlns="http://www.opengis.net/kml/2.2">
      <Document>
        <Placemark>
          <Polygon>
            <outerBoundaryIs>
              <LinearRing>
                <coordinates>
                  106.827,-6.175,0 106.828,-6.175,0 106.828,-6.176,0 106.827,-6.176,0 106.827,-6.175,0
                </coordinates>
              </LinearRing>
            </outerBoundaryIs>
          </Polygon>
        </Placemark>
      </Document>
    </kml>
    KML;

    return UploadedFile::fake()->createWithContent($namaBerkas, $isi);
}

// TS.LKA.001 / TC.LKA.001.001 — Positive
test('admin berhasil mengunggah berkas area absensi', function () {
    adminLokasiAbsen();

    $this->get('/admin/pengaturan/lokasi-absen')->assertSee('Lokasi Absen');

    $this->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen', [
            'kml_file' => berkasAreaSekolah(),
        ])
        ->assertSee('Area absensi berhasil diimpor.');
});

// TS.LKA.002 / TC.LKA.002.001 — Negative
test('admin gagal mengunggah berkas yang bukan berkas area', function () {
    adminLokasiAbsen();

    $this->from('/admin/pengaturan/lokasi-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen', [
            'kml_file' => UploadedFile::fake()->create('gambar.jpg', 100, 'image/jpeg'),
        ])
        ->assertSee('File harus berformat KML.')
        ->assertDontSee('Area absensi berhasil diimpor.');
});

// TS.LKA.003 / TC.LKA.003.001 — Negative
test('admin gagal mengunggah berkas area yang tidak memuat gambar area', function () {
    adminLokasiAbsen();

    $tanpaArea = <<<'KML'
    <?xml version="1.0" encoding="UTF-8"?>
    <kml xmlns="http://www.opengis.net/kml/2.2">
      <Document><Placemark><Point><coordinates>106.827,-6.175,0</coordinates></Point></Placemark></Document>
    </kml>
    KML;

    $this->from('/admin/pengaturan/lokasi-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen', [
            'kml_file' => UploadedFile::fake()->createWithContent('titik.kml', $tanpaArea),
        ])
        ->assertSee('Tidak ditemukan polygon dalam file KML.');
});

// TS.LKA.004 / TC.LKA.004.001 — Negative
test('admin gagal mengunggah berkas area yang titiknya kurang dari tiga', function () {
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
            'kml_file' => UploadedFile::fake()->createWithContent('kurang.kml', $duaTitik),
        ])
        ->assertSee('Polygon harus memiliki minimal 3 titik koordinat unik.');
});

// TS.LKA.005 / TC.LKA.005.001 — Positive
test('admin berhasil menyimpan toleransi jarak', function () {
    adminLokasiAbsen();

    $this->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen/tolerance', [
            'tolerance_meters' => 50,
        ])
        ->assertSee('Toleransi jarak berhasil disimpan.');
});

// TS.LKA.006 / TC.LKA.006.001 — Negative
test('admin gagal menyimpan toleransi jarak yang bukan angka', function () {
    adminLokasiAbsen();

    $this->from('/admin/pengaturan/lokasi-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen/tolerance', [
            'tolerance_meters' => 'lima puluh',
        ])
        ->assertSee('Toleransi harus berupa angka.');
});

// TS.LKA.007 / TC.LKA.007.001 — Positive
test('admin berhasil menghapus area absensi yang sudah dipasang', function () {
    adminLokasiAbsen();

    $this->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen', ['kml_file' => berkasAreaSekolah()])
        ->assertSee('Area absensi berhasil diimpor.');

    $this->followingRedirects()
        ->delete('/admin/pengaturan/lokasi-absen')
        ->assertSee('Lokasi absen berhasil dihapus.');
});
