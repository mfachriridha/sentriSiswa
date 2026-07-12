<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImporSiswa implements ToCollection, WithChunkReading, WithHeadingRow
{
    public int $studentsCreated = 0;

    public int $studentsExisting = 0;

    public int $errors = 0;

    public array $errorDetails = [];

    protected string $defaultPassword;

    protected array $classCache = [];

    protected int $rowIndex = 0;

    /**
     * NISN yang sudah dipakai baris sebelumnya di berkas yang sama, beserta nomor
     * barisnya. Dipakai untuk menangkap NISN ganda di dalam satu berkas.
     *
     * @var array<string, int>
     */
    protected array $nisnTerpakai = [];

    public function __construct()
    {
        $this->defaultPassword = Hash::make('password');
    }

    public function collection(Collection $rows): void
    {
        $newUsers = [];
        $profileRows = [];

        foreach ($rows as $row) {
            $this->rowIndex++;
            $baris = self::rapikanBaris($row);
            $nama = $baris['nama'];
            $nis = $baris['nis'];
            $nisn = $baris['nisn'];
            $jenisKelamin = $baris['jenis_kelamin'];
            $kelas = $baris['kelas'];

            if ($baris['alasan_dilewati'] !== null) {
                $this->errors++;
                $this->errorDetails[] = [
                    'row' => $this->rowIndex,
                    'nama' => $nama === '' ? '(kosong)' : $nama,
                    'reason' => $baris['alasan_dilewati'],
                ];

                continue;
            }

            // NISN adalah penanda siswa yang harus unik. Kalau satu berkas memuat
            // dua baris ber-NISN sama, baris kedua tidak boleh ikut masuk: dulu
            // akunnya tetap dibuat tetapi profilnya menimpa profil baris pertama,
            // sehingga siswa yang pertama hilang tanpa peringatan apa pun.
            if (isset($this->nisnTerpakai[$nisn])) {
                $this->errors++;
                $this->errorDetails[] = [
                    'row' => $this->rowIndex,
                    'nama' => $nama,
                    'reason' => 'NISN ganda di dalam berkas ('.$nisn.'), sudah dipakai baris '.$this->nisnTerpakai[$nisn],
                ];

                continue;
            }

            $this->nisnTerpakai[$nisn] = $this->rowIndex;

            $kelasId = null;
            if ($kelas) {
                if (! isset($this->classCache[$kelas])) {
                    $tingkat = (int) strtok($kelas, ' .-');
                    $kelasObj = Kelas::firstOrCreate(
                        ['nama' => $kelas],
                        ['tingkat' => in_array($tingkat, [10, 11, 12]) ? (string) $tingkat : '10']
                    );
                    $this->classCache[$kelas] = $kelasObj->id;
                }
                $kelasId = $this->classCache[$kelas];
            }

            $newUsers[] = [
                'nama' => $nama,
                'peran' => 'siswa',
                'status' => 'unregistered',
                'password' => $this->defaultPassword,
                'email' => null,
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ];

            $profileRows[] = [
                'nama' => $nama,
                'nisn' => $nisn,
                'nis' => $nis,
                'jenis_kelamin' => $jenisKelamin,
                'kelas_id' => $kelasId,
                'row' => $this->rowIndex,
            ];
        }

        if (empty($newUsers)) {
            return;
        }

        DB::transaction(function () use ($newUsers, $profileRows) {
            $existingNisns = collect($profileRows)
                ->pluck('nisn')
                ->filter()
                ->toArray();

            $existingProfiles = [];
            if (! empty($existingNisns)) {
                $existingProfiles = ProfilSiswa::whereIn('nisn', $existingNisns)
                    ->pluck('pengguna_id', 'nisn')
                    ->toArray();
            }

            $freshUsers = [];
            $profileData = [];
            $existingCount = 0;

            foreach ($profileRows as $i => $profile) {
                $nama = $profile['nama'];
                $nisn = $profile['nisn'];

                if ($nisn && isset($existingProfiles[$nisn])) {
                    $existingCount++;
                    $this->errorDetails[] = [
                        'row' => $profile['row'],
                        'nama' => $nama,
                        'reason' => 'NISN sudah ada di database ('.$nisn.')',
                    ];
                    $profileData[] = [
                        'pengguna_id' => $existingProfiles[$nisn],
                        'nisn' => $nisn,
                        'nis' => $profile['nis'],
                        'jenis_kelamin' => $profile['jenis_kelamin'],
                        'kelas_id' => $profile['kelas_id'],
                        'diperbarui_pada' => now(),
                    ];
                } else {
                    $freshUsers[] = $newUsers[$i];
                    $profileData[] = [
                        'pengguna_id' => null,
                        'nisn' => $nisn,
                        'nis' => $profile['nis'],
                        'jenis_kelamin' => $profile['jenis_kelamin'],
                        'kelas_id' => $profile['kelas_id'],
                    ];
                }
            }

            foreach (array_chunk($freshUsers, 500) as $chunk) {
                Pengguna::insert($chunk);
            }

            $this->studentsCreated += count($freshUsers);
            $this->studentsExisting += $existingCount;

            if (count($freshUsers) > 0) {
                $firstId = Pengguna::where('peran', 'siswa')->latest('id')->first()->id;
                $newUserOffset = $firstId - count($freshUsers) + 1;
            } else {
                $newUserOffset = 0;
            }

            $inserts = [];
            $newIdx = 0;

            foreach ($profileData as $profile) {
                if ($profile['pengguna_id'] === null) {
                    $newIdx++;

                    if ($newIdx > count($freshUsers)) {
                        continue;
                    }

                    $inserts[] = [
                        'pengguna_id' => $newUserOffset + $newIdx - 1,
                        'nisn' => $profile['nisn'],
                        'nis' => $profile['nis'],
                        'jenis_kelamin' => $profile['jenis_kelamin'],
                        'kelas_id' => $profile['kelas_id'],
                        'dibuat_pada' => now(),
                        'diperbarui_pada' => now(),
                    ];
                } else {
                    $inserts[] = [
                        'pengguna_id' => $profile['pengguna_id'],
                        'nisn' => $profile['nisn'],
                        'nis' => $profile['nis'],
                        'jenis_kelamin' => $profile['jenis_kelamin'],
                        'kelas_id' => $profile['kelas_id'],
                        'diperbarui_pada' => now(),
                    ];
                }
            }

            foreach (array_chunk($inserts, 500) as $chunk) {
                ProfilSiswa::upsert($chunk, ['nisn'], ['pengguna_id', 'nis', 'jenis_kelamin', 'kelas_id', 'diperbarui_pada']);
            }
        });
    }

    /**
     * Membaca seluruh isi berkas impor, lalu memutuskan baris mana yang bisa
     * diimpor dan baris mana yang harus dilewati beserta alasannya.
     *
     * Halaman tinjauan memakai fungsi yang sama persis dengan proses impor,
     * supaya keduanya tidak pernah berbeda pendapat: yang ditandai bermasalah di
     * tinjauan pasti dilewati saat impor, dan sebaliknya. NISN ganda hanya bisa
     * ditemukan dengan melihat seluruh berkas sekaligus, bukan baris per baris,
     * jadi pemeriksaannya dikerjakan di sini.
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array{nama: string, nis: ?string, nisn: ?string, jenis_kelamin: ?string, kelas: ?string, alasan_dilewati: ?string}>
     */
    public static function rapikanBerkas(array $rows): array
    {
        $hasil = [];
        $nisnTerpakai = [];

        foreach ($rows as $indeks => $row) {
            $baris = self::rapikanBaris($row);
            $nomorBaris = $indeks + 1;
            $nisn = $baris['nisn'];

            if ($baris['alasan_dilewati'] === null && isset($nisnTerpakai[$nisn])) {
                $baris['alasan_dilewati'] = 'NISN ganda di dalam berkas ('.$nisn.'), sudah dipakai baris '.$nisnTerpakai[$nisn];
            } elseif ($baris['alasan_dilewati'] === null) {
                $nisnTerpakai[$nisn] = $nomorBaris;
            }

            $hasil[] = $baris;
        }

        return $hasil;
    }

    /**
     * Membaca satu baris berkas impor apa adanya, lalu memutuskan apakah baris
     * itu bisa diimpor atau harus dilewati.
     *
     * Pemeriksaan NISN ganda tidak ada di sini, karena butuh melihat baris lain.
     * Lihat rapikanBerkas().
     *
     * @param  array<string, mixed>|Collection<string, mixed>  $row
     * @return array{nama: string, nis: ?string, nisn: ?string, jenis_kelamin: ?string, kelas: ?string, alasan_dilewati: ?string}
     */
    public static function rapikanBaris(mixed $row): array
    {
        $nama = trim((string) ($row['nama'] ?? ''));
        $nis = self::bersihkanNomor($row['nis'] ?? null);
        $nisn = self::bersihkanNomor($row['nisn'] ?? null);
        $nisn = $nisn !== null ? str_pad($nisn, 10, '0', STR_PAD_LEFT) : null;

        $alasan = match (true) {
            $nama === '' => 'Nama kosong',
            $nisn === null => 'NISN kosong',
            $nis === null => 'NIS kosong',
            ! ctype_digit($nis) => 'NIS harus berupa angka ('.$nis.')',
            ! ctype_digit($nisn) => 'NISN harus berupa angka ('.$nisn.')',
            mb_strlen($nis) > 15 => 'NIS terlalu panjang, maksimal 15 angka ('.$nis.')',
            mb_strlen($nisn) > 10 => 'NISN terlalu panjang, maksimal 10 angka ('.$nisn.')',
            default => null,
        };

        return [
            'nama' => $nama,
            'nis' => $nis,
            'nisn' => $nisn,
            'jenis_kelamin' => self::bacaJenisKelamin($row),
            'kelas' => isset($row['kelas']) ? trim((string) $row['kelas']) : null,
            'alasan_dilewati' => $alasan,
        ];
    }

    /**
     * Membersihkan NIS/NISN dari sisa penulisan Excel.
     *
     * Sel yang diformat sebagai teks sering menyimpan tanda kutip di depan
     * angkanya ('0084814788). Tanda itu, spasi, dan pemisah ribuan dibuang dulu
     * supaya angkanya bisa diperiksa apa adanya, bukan ikut tersimpan ke data.
     */
    private static function bersihkanNomor(mixed $nilai): ?string
    {
        if ($nilai === null) {
            return null;
        }

        $bersih = trim((string) $nilai);
        $bersih = trim($bersih, "'`\"\u{2018}\u{2019}");
        $bersih = str_replace([' ', '.', ',', "\u{00A0}"], '', $bersih);

        return $bersih === '' ? null : $bersih;
    }

    /**
     * Membaca jenis kelamin dari kolom mana pun yang dipakai sekolah, entah
     * berjudul "L/P", "Jenis Kelamin", atau "Gender", dan menerima isian
     * "L"/"P" maupun "Laki-laki"/"Perempuan".
     *
     * @param  array<string, mixed>|Collection<string, mixed>  $row
     */
    private static function bacaJenisKelamin(mixed $row): ?string
    {
        foreach (['jenis_kelamin', 'l_p', 'lp', 'gender'] as $kolom) {
            $nilai = trim((string) ($row[$kolom] ?? ''));

            if ($nilai === '') {
                continue;
            }

            return match (mb_strtolower($nilai)) {
                'l', 'laki-laki', 'laki laki', 'pria' => 'L',
                'p', 'perempuan', 'wanita' => 'P',
                default => null,
            };
        }

        return null;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
