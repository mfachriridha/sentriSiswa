<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeacherImport implements ToCollection, WithChunkReading, WithHeadingRow
{
    public int $teachersCreated = 0;

    public int $teachersExisting = 0;

    public int $classesCreated = 0;

    public int $errors = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            DB::transaction(function () use ($row) {
                $teacherName = trim((string) ($row['walas'] ?? ''));
                $className = trim((string) ($row['kelas'] ?? ''));
                $nip = isset($row['nip']) && $row['nip'] !== '-' ? str_replace(' ', '', trim((string) $row['nip'])) : null;

                if (empty($teacherName)) {
                    $this->errors++;

                    return;
                }

                $grade = $className ? (int) strtok($className, ' .-') : null;

                if ($className && $grade) {
                    $class = SchoolClass::firstOrCreate(
                        ['name' => $className],
                        ['grade' => in_array($grade, [10, 11, 12]) ? (string) $grade : '10']
                    );
                    $this->classesCreated++;
                }

                $user = User::firstOrCreate(
                    ['name' => $teacherName, 'role' => 'teacher'],
                    [
                        'password' => Hash::make(Str::random(16)),
                        'role' => 'teacher',
                    ]
                );

                if ($user->wasRecentlyCreated) {
                    $this->teachersCreated++;
                } else {
                    $this->teachersExisting++;
                }

                $user->teacherProfile()->firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip' => $nip,
                        'teacher_type' => 'homeroom',
                    ]
                );

                if (isset($class)) {
                    $class->update(['homeroom_teacher_id' => $user->id]);
                }
            });
        }
    }

    public function chunkSize(): int
    {
        return 50;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
