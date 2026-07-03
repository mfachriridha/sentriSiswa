<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilGuru;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeacherImport implements ToCollection, WithChunkReading, WithHeadingRow
{
    public int $teachersCreated = 0;

    public int $teachersExisting = 0;

    public int $classesCreated = 0;

    public int $errors = 0;

    public array $errorDetails = [];

    protected string $defaultPassword;

    protected array $classCache = [];

    protected bool $isOldFormat = false;

    protected int $rowIndex = 0;

    public function __construct()
    {
        $this->defaultPassword = Hash::make('password');
    }

    public function collection(Collection $rows): void
    {
        if ($rows->isNotEmpty()) {
            $firstRow = $rows->first();
            $this->isOldFormat = isset($firstRow['walas']);
        }

        $newUsers = [];
        $profileRows = [];

        foreach ($rows as $row) {
            $this->rowIndex++;
            $parsed = $this->parseRow($row);

            if (empty($parsed['nama'])) {
                $this->errors++;
                $this->errorDetails[] = ['row' => $this->rowIndex, 'nama' => '(kosong)', 'reason' => 'Nama kosong'];

                continue;
            }

            if (empty($parsed['nip'])) {
                $this->errors++;
                $this->errorDetails[] = ['row' => $this->rowIndex, 'nama' => $parsed['nama'], 'reason' => 'NIP kosong, guru harus didaftarkan manual oleh admin'];

                continue;
            }

            $tingkat = $parsed['className'] ? (int) strtok($parsed['className'], ' .-') : null;

            if ($parsed['className'] && $tingkat) {
                if (! isset($this->classCache[$parsed['className']])) {
                    $kelasObj = Kelas::firstOrCreate(
                        ['nama' => $parsed['className']],
                        ['tingkat' => in_array($tingkat, [10, 11, 12]) ? (string) $tingkat : '10']
                    );
                    $this->classCache[$parsed['className']] = $kelasObj->id;
                    $this->classesCreated++;
                }
            }

            $newUsers[] = [
                'nama' => $parsed['nama'],
                'peran' => $parsed['peran'],
                'status' => 'unregistered',
                'password' => $this->defaultPassword,
                'email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $profileRows[] = [
                'nama' => $parsed['nama'],
                'nip' => $parsed['nip'],
                'peran' => $parsed['peran'],
                'className' => $parsed['className'],
                'tingkat' => $parsed['tingkat'],
            ];
        }

        if (empty($newUsers)) {
            return;
        }

        DB::transaction(function () use ($newUsers, $profileRows) {
            $nips = collect($profileRows)->pluck('nip')->filter()->toArray();

            $existingProfiles = [];
            if (! empty($nips)) {
                $existingProfiles = ProfilGuru::whereIn('nip', $nips)
                    ->pluck('pengguna_id', 'nip')
                    ->toArray();
            }

            $freshUsers = [];
            $existingCount = 0;

            foreach ($profileRows as $i => $profile) {
                $nip = $profile['nip'];

                if ($nip && isset($existingProfiles[$nip])) {
                    $existingCount++;
                } else {
                    $freshUsers[] = $newUsers[$i];
                }
            }

            foreach (array_chunk($freshUsers, 500) as $chunk) {
                Pengguna::insert($chunk);
            }

            $this->teachersCreated += count($freshUsers);
            $this->teachersExisting += $existingCount;

            if (count($freshUsers) > 0) {
                $firstId = Pengguna::whereIn('peran', ['wali_kelas', 'bk', 'kesiswaan'])->latest('id')->first()->id;
                $newUserOffset = $firstId - count($freshUsers) + 1;
            } else {
                $newUserOffset = 0;
            }

            $inserts = [];
            $newIdx = 0;

            foreach ($profileRows as $profile) {
                $nip = $profile['nip'];
                $className = $profile['className'];
                $penggunaId = null;

                if ($nip && isset($existingProfiles[$nip])) {
                    $penggunaId = $existingProfiles[$nip];
                } else {
                    $newIdx++;

                    if ($newIdx > count($freshUsers)) {
                        continue;
                    }

                    $penggunaId = $newUserOffset + $newIdx - 1;
                }

                $inserts[] = [
                    'pengguna_id' => $penggunaId,
                    'nip' => $nip,
                    'tingkat' => $profile['tingkat'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($profile['peran'] === 'wali_kelas' && $className && isset($this->classCache[$className]) && $penggunaId) {
                    Kelas::where('id', $this->classCache[$className])
                        ->update(['wali_kelas_id' => $penggunaId]);
                }
            }

            foreach (array_chunk($inserts, 500) as $chunk) {
                ProfilGuru::upsert($chunk, ['nip'], ['pengguna_id', 'tingkat', 'updated_at']);
            }
        });
    }

    protected function parseRow(Collection $row): array
    {
        if ($this->isOldFormat) {
            $nama = trim((string) ($row['walas'] ?? ''));
            $nipRaw = isset($row['nip']) && $row['nip'] !== '-' ? trim((string) $row['nip']) : null;
            $nip = $nipRaw ? str_replace(' ', '', $nipRaw) : null;
            $className = trim((string) ($row['kelas'] ?? ''));
            $tingkat = null;

            if ($className) {
                $tingkatNum = (int) strtok($className, ' .-');
                $tingkat = in_array($tingkatNum, [10, 11, 12]) ? (string) $tingkatNum : null;
            }

            return [
                'nama' => $nama,
                'nip' => $nip,
                'peran' => 'wali_kelas',
                'className' => $className,
                'tingkat' => $tingkat,
            ];
        }

        $nama = trim((string) ($row['nama'] ?? ''));
        $nipRaw = isset($row['nip']) && $row['nip'] !== '-' ? trim((string) $row['nip']) : null;
        $nip = $nipRaw ? str_replace(' ', '', $nipRaw) : null;
        $className = trim((string) ($row['kelas'] ?? ''));

        $typeRaw = trim((string) ($row['tipe'] ?? ''));
        $peran = in_array(strtolower($typeRaw), ['bk', 'guru bk'], true) ? 'bk'
            : (in_array(strtolower($typeRaw), ['kesiswaan', 'student_affairs'], true) ? 'kesiswaan' : 'wali_kelas');

        $tingkat = null;
        if ($peran === 'bk' && ! empty($typeRaw)) {
            $tingkatNum = (int) strtok($className ? $className : '0', ' .-');
            $tingkat = in_array($tingkatNum, [10, 11, 12]) ? (string) $tingkatNum : null;
        }

        return [
            'nama' => $nama,
            'nip' => $nip,
            'peran' => $peran,
            'className' => $className,
            'tingkat' => $tingkat,
        ];
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
