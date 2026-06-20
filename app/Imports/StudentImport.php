<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToCollection, WithChunkReading, WithHeadingRow
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
            $name = trim((string) ($row['nama'] ?? ''));
            $nisRaw = isset($row['nis']) ? trim((string) $row['nis']) : null;
            $nis = $nisRaw ? mb_substr($nisRaw, 0, 20) : null;
            $nisnRaw = isset($row['nisn']) ? trim((string) $row['nisn']) : null;
            $nisnRaw = is_numeric($nisnRaw) ? str_pad((string) $nisnRaw, 10, '0', STR_PAD_LEFT) : $nisnRaw;
            $nisn = $nisnRaw ? mb_substr((string) $nisnRaw, 0, 10) : null;
            $kelas = isset($row['kelas']) ? trim((string) $row['kelas']) : null;

            if (empty($name)) {
                $this->errors++;
                $this->errorDetails[] = ['row' => $this->rowIndex, 'name' => '(kosong)', 'reason' => 'Nama kosong'];

                continue;
            }

            $classId = null;
            if ($kelas) {
                if (! isset($this->classCache[$kelas])) {
                    $grade = (int) strtok($kelas, ' .-');
                    $class = SchoolClass::firstOrCreate(
                        ['name' => $kelas],
                        ['grade' => in_array($grade, [10, 11, 12]) ? (string) $grade : '10']
                    );
                    $this->classCache[$kelas] = $class->id;
                }
                $classId = $this->classCache[$kelas];
            }

            $newUsers[] = [
                'name' => $name,
                'role' => 'siswa',
                'status' => 'unregistered',
                'password' => $this->defaultPassword,
                'email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $profileRows[] = [
                'name' => $name,
                'nisn' => $nisn,
                'nis' => $nis,
                'class_id' => $classId,
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
                $existingProfiles = StudentProfile::whereIn('nisn', $existingNisns)
                    ->pluck('user_id', 'nisn')
                    ->toArray();
            }

            $freshUsers = [];
            $profileData = [];
            $existingCount = 0;

            foreach ($profileRows as $i => $profile) {
                $name = $profile['name'];
                $nisn = $profile['nisn'];

                if ($nisn && isset($existingProfiles[$nisn])) {
                    $existingCount++;
                    $this->errorDetails[] = [
                        'row' => $profile['row'],
                        'name' => $name,
                        'reason' => 'NISN sudah ada di database ('.$nisn.')',
                    ];
                    $profileData[] = [
                        'user_id' => $existingProfiles[$nisn],
                        'nisn' => $nisn,
                        'nis' => $profile['nis'],
                        'class_id' => $profile['class_id'],
                        'updated_at' => now(),
                    ];
                } else {
                    $freshUsers[] = $newUsers[$i];
                    $profileData[] = [
                        'user_id' => null,
                        'nisn' => $nisn,
                        'nis' => $profile['nis'],
                        'class_id' => $profile['class_id'],
                    ];
                }
            }

            foreach (array_chunk($freshUsers, 500) as $chunk) {
                User::insert($chunk);
            }

            $this->studentsCreated += count($freshUsers);
            $this->studentsExisting += $existingCount;

            if (count($freshUsers) > 0) {
                $firstId = User::where('role', 'siswa')->latest('id')->first()->id;
                $newUserOffset = $firstId - count($freshUsers) + 1;
            } else {
                $newUserOffset = 0;
            }

            $inserts = [];
            $newIdx = 0;

            foreach ($profileData as $profile) {
                if ($profile['user_id'] === null) {
                    $newIdx++;

                    if ($newIdx > count($freshUsers)) {
                        continue;
                    }

                    $inserts[] = [
                        'user_id' => $newUserOffset + $newIdx - 1,
                        'nisn' => $profile['nisn'],
                        'nis' => $profile['nis'],
                        'class_id' => $profile['class_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    $inserts[] = [
                        'user_id' => $profile['user_id'],
                        'nisn' => $profile['nisn'],
                        'nis' => $profile['nis'],
                        'class_id' => $profile['class_id'],
                        'updated_at' => now(),
                    ];
                }
            }

            foreach (array_chunk($inserts, 500) as $chunk) {
                StudentProfile::upsert($chunk, ['user_id'], ['nisn', 'nis', 'class_id', 'updated_at']);
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
