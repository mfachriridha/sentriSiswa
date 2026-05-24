<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\TeacherProfile;
use App\Models\User;
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

    protected string $defaultPassword;

    protected array $classCache = [];

    protected bool $isOldFormat = false;

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
            $parsed = $this->parseRow($row);

            if (empty($parsed['name'])) {
                $this->errors++;

                continue;
            }

            $grade = $parsed['className'] ? (int) strtok($parsed['className'], ' .-') : null;

            if ($parsed['className'] && $grade) {
                if (! isset($this->classCache[$parsed['className']])) {
                    $class = SchoolClass::firstOrCreate(
                        ['name' => $parsed['className']],
                        ['grade' => in_array($grade, [10, 11, 12]) ? (string) $grade : '10']
                    );
                    $this->classCache[$parsed['className']] = $class->id;
                    $this->classesCreated++;
                }
            }

            $newUsers[] = [
                'name' => $parsed['name'],
                'role' => 'teacher',
                'password' => $this->defaultPassword,
                'email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $profileRows[] = [
                'name' => $parsed['name'],
                'nip' => $parsed['nip'],
                'teacher_type' => $parsed['teacherType'],
                'className' => $parsed['className'],
                'grade' => $parsed['grade'],
            ];
        }

        if (empty($newUsers)) {
            return;
        }

        DB::transaction(function () use ($newUsers, $profileRows) {
            $nips = collect($profileRows)->pluck('nip')->filter()->toArray();

            $existingProfiles = [];
            if (! empty($nips)) {
                $existingProfiles = TeacherProfile::whereIn('nip', $nips)
                    ->pluck('user_id', 'nip')
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
                User::insert($chunk);
            }

            $this->teachersCreated += count($freshUsers);
            $this->teachersExisting += $existingCount;

            if (count($freshUsers) > 0) {
                $firstId = User::where('role', 'teacher')->latest('id')->first()->id;
                $newUserOffset = $firstId - count($freshUsers) + 1;
            } else {
                $newUserOffset = 0;
            }

            $inserts = [];
            $newIdx = 0;

            foreach ($profileRows as $profile) {
                $nip = $profile['nip'];
                $className = $profile['className'];
                $userId = null;

                if ($nip && isset($existingProfiles[$nip])) {
                    $userId = $existingProfiles[$nip];
                } else {
                    $newIdx++;

                    if ($newIdx > count($freshUsers)) {
                        continue;
                    }

                    $userId = $newUserOffset + $newIdx - 1;
                }

                $inserts[] = [
                    'user_id' => $userId,
                    'nip' => $nip,
                    'teacher_type' => $profile['teacherType'],
                    'grade' => $profile['grade'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($profile['teacherType'] === 'homeroom' && $className && isset($this->classCache[$className]) && $userId) {
                    SchoolClass::where('id', $this->classCache[$className])
                        ->update(['homeroom_teacher_id' => $userId]);
                }
            }

            foreach (array_chunk($inserts, 500) as $chunk) {
                TeacherProfile::upsert($chunk, ['user_id'], ['nip', 'teacher_type', 'grade', 'updated_at']);
            }
        });
    }

    protected function parseRow(Collection $row): array
    {
        if ($this->isOldFormat) {
            $name = trim((string) ($row['walas'] ?? ''));
            $nipRaw = isset($row['nip']) && $row['nip'] !== '-' ? trim((string) $row['nip']) : null;
            $nip = $nipRaw ? str_replace(' ', '', $nipRaw) : null;
            $className = trim((string) ($row['kelas'] ?? ''));
            $grade = null;

            if ($className) {
                $gradeNum = (int) strtok($className, ' .-');
                $grade = in_array($gradeNum, [10, 11, 12]) ? (string) $gradeNum : null;
            }

            return [
                'name' => $name,
                'nip' => $nip,
                'teacherType' => 'homeroom',
                'className' => $className,
                'grade' => $grade,
            ];
        }

        $name = trim((string) ($row['nama'] ?? ''));
        $nipRaw = isset($row['nip']) && $row['nip'] !== '-' ? trim((string) $row['nip']) : null;
        $nip = $nipRaw ? str_replace(' ', '', $nipRaw) : null;
        $className = trim((string) ($row['kelas'] ?? ''));

        $typeRaw = trim((string) ($row['tipe'] ?? ''));
        $teacherType = in_array(strtolower($typeRaw), ['bk', 'guru bk']) ? 'counselor' : 'homeroom';

        $grade = null;
        if ($teacherType === 'counselor' && ! empty($typeRaw)) {
            $gradeNum = (int) strtok($className ? $className : '0', ' .-');
            $grade = in_array($gradeNum, [10, 11, 12]) ? (string) $gradeNum : null;
        }

        return [
            'name' => $name,
            'nip' => $nip,
            'teacherType' => $teacherType,
            'className' => $className,
            'grade' => $grade,
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
