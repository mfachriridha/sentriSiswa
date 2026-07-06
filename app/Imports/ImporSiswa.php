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
            $nama = trim((string) ($row['nama'] ?? ''));
            $nisRaw = isset($row['nis']) ? trim((string) $row['nis']) : null;
            $nis = $nisRaw ? mb_substr($nisRaw, 0, 15) : null;
            $nisnRaw = isset($row['nisn']) ? trim((string) $row['nisn']) : null;
            $nisnRaw = is_numeric($nisnRaw) ? str_pad((string) $nisnRaw, 10, '0', STR_PAD_LEFT) : $nisnRaw;
            $nisn = $nisnRaw ? mb_substr((string) $nisnRaw, 0, 10) : null;
            $kelas = isset($row['kelas']) ? trim((string) $row['kelas']) : null;

            if (empty($nama)) {
                $this->errors++;
                $this->errorDetails[] = ['row' => $this->rowIndex, 'nama' => '(kosong)', 'reason' => 'Nama kosong'];

                continue;
            }

            if (empty($nisn)) {
                $this->errors++;
                $this->errorDetails[] = ['row' => $this->rowIndex, 'nama' => $nama, 'reason' => 'NISN kosong'];

                continue;
            }

            if (empty($nis)) {
                $this->errors++;
                $this->errorDetails[] = ['row' => $this->rowIndex, 'nama' => $nama, 'reason' => 'NIS kosong'];

                continue;
            }

            if (! ctype_digit($nis)) {
                $this->errors++;
                $this->errorDetails[] = ['row' => $this->rowIndex, 'nama' => $nama, 'reason' => 'NIS harus berupa angka ('.$nis.')'];

                continue;
            }

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
                        'kelas_id' => $profile['kelas_id'],
                        'diperbarui_pada' => now(),
                    ];
                } else {
                    $freshUsers[] = $newUsers[$i];
                    $profileData[] = [
                        'pengguna_id' => null,
                        'nisn' => $nisn,
                        'nis' => $profile['nis'],
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
                        'kelas_id' => $profile['kelas_id'],
                        'dibuat_pada' => now(),
                        'diperbarui_pada' => now(),
                    ];
                } else {
                    $inserts[] = [
                        'pengguna_id' => $profile['pengguna_id'],
                        'nisn' => $profile['nisn'],
                        'nis' => $profile['nis'],
                        'kelas_id' => $profile['kelas_id'],
                        'diperbarui_pada' => now(),
                    ];
                }
            }

            foreach (array_chunk($inserts, 500) as $chunk) {
                ProfilSiswa::upsert($chunk, ['nisn'], ['pengguna_id', 'nis', 'kelas_id', 'diperbarui_pada']);
            }
        });
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
