<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\TeacherClass;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WaliKelasSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('untuk-refrensi/dbwalas.csv');
        $handle = fopen($path, 'r');

        fgetcsv($handle, 0, ';');

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            [$kelas, $walas, $nip] = $row;

            $schoolClass = SchoolClass::where('name', $kelas)->first();
            if (! $schoolClass) {
                $this->command->warn("Kelas '$kelas' tidak ditemukan, skip.");

                continue;
            }

            $nip = trim($nip);

            if ($nip === '-' || $nip === '') {
                $email = str_replace(' ', '', strtolower($walas));
                // Keep only letters, no special chars
                $email = preg_replace('/[^a-z]/', '', $email);
                $email = substr($email, 0, 30).'@sentrisiswa.sch.id';
                $nipValue = null;
            } else {
                $nipDigits = preg_replace('/\s+/', '', $nip);
                $email = $nipDigits.'@sentrisiswa.sch.id';
                $nipValue = $nip;
            }

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $walas,
                    'nip' => $nipValue,
                    'role' => 'wali_kelas',
                    'is_active' => false,
                    'password' => Hash::make('password123'),
                ]
            );

            TeacherClass::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'school_class_id' => $schoolClass->id,
                    'role' => 'wali_kelas',
                ]
            );

            $this->command->info("{$walas} → {$kelas}");
        }

        fclose($handle);
        $this->command->info('Selesai: 38 wali kelas diimport.');
    }
}
