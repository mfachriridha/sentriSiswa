<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;

class CsvImportService
{
    public function importGuru(string $csvPath): array
    {
        $result = ['success' => 0, 'failed' => 0, 'errors' => []];

        $handle = fopen($csvPath, 'r');
        if (! $handle) {
            return ['success' => 0, 'failed' => 0, 'errors' => ['Tidak dapat membuka file.']];
        }

        $header = fgetcsv($handle, 0, ';');
        $rowNumber = 1;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $rowNumber++;

            if (count($row) < 2) {
                $result['failed']++;
                $result['errors'][] = "Baris {$rowNumber}: Format tidak valid.";

                continue;
            }

            $nama = trim($row[0]);
            $nip = str_replace(' ', '', trim($row[1]));

            if (empty($nama) || empty($nip)) {
                $result['failed']++;
                $result['errors'][] = "Baris {$rowNumber}: Nama atau NIP kosong.";

                continue;
            }

            try {
                Guru::updateOrCreate(
                    ['nip' => $nip],
                    ['nama' => $nama]
                );
                $result['success']++;
            } catch (\Throwable $e) {
                $result['failed']++;
                $result['errors'][] = "Baris {$rowNumber}: {$e->getMessage()}";
            }
        }

        fclose($handle);

        return $result;
    }

    public function importSiswa(string $csvPath): array
    {
        $result = ['success' => 0, 'failed' => 0, 'errors' => []];

        $handle = fopen($csvPath, 'r');
        if (! $handle) {
            return ['success' => 0, 'failed' => 0, 'errors' => ['Tidak dapat membuka file.']];
        }

        $header = fgetcsv($handle, 0, ';');
        $rowNumber = 1;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $rowNumber++;

            if (count($row) < 5) {
                $result['failed']++;
                $result['errors'][] = "Baris {$rowNumber}: Format tidak valid.";

                continue;
            }

            $nama = trim($row[0]);
            $jenisKelamin = trim($row[1]);
            $namaKelas = trim($row[2]);
            $nis = str_replace(' ', '', trim($row[3]));
            $nisn = str_replace(' ', '', trim($row[4]));

            if (empty($nama) || empty($nis) || empty($namaKelas)) {
                $result['failed']++;
                $result['errors'][] = "Baris {$rowNumber}: Nama, NIS, atau Kelas kosong.";

                continue;
            }

            $jenisKelamin = match (mb_strtolower($jenisKelamin)) {
                'l', 'laki-laki', 'laki laki', 'pria', 'male' => 'L',
                'p', 'perempuan', 'wanita', 'female' => 'P',
                default => 'L',
            };

            try {
                $kelas = $this->resolveKelas($namaKelas);

                Siswa::updateOrCreate(
                    ['nis' => $nis],
                    [
                        'nama' => $nama,
                        'nisn' => $nisn ?: null,
                        'jenis_kelamin' => $jenisKelamin,
                        'kelas_id' => $kelas->id,
                    ]
                );
                $result['success']++;
            } catch (\Throwable $e) {
                $result['failed']++;
                $result['errors'][] = "Baris {$rowNumber}: {$e->getMessage()}";
            }
        }

        fclose($handle);

        return $result;
    }

    private function resolveKelas(string $namaKelas): Kelas
    {
        $kelas = Kelas::where('nama', $namaKelas)->first();
        if ($kelas) {
            return $kelas;
        }

        $parts = explode(' ', $namaKelas);

        if (count($parts) >= 2) {
            $tingkatMap = [
                '10' => 10, 'x' => 10, 'X' => 10,
                '11' => 11, 'xi' => 11, 'XI' => 11,
                '12' => 12, 'xii' => 12, 'XII' => 12,
            ];

            $first = $parts[0];
            if (isset($tingkatMap[$first])) {
                $tingkat = $tingkatMap[$first];
            } elseif (is_numeric($first) && in_array((int) $first, [10, 11, 12])) {
                $tingkat = (int) $first;
            } else {
                $tingkat = 10;
            }

            $jurusanMap = [
                'ipa' => 'IPA', 'ips' => 'IPS',
                'bahasa' => 'Bahasa', 'bhs' => 'Bahasa',
            ];

            $jurusan = 'IPA';
            foreach ($parts as $part) {
                $lower = mb_strtolower($part);
                if (isset($jurusanMap[$lower])) {
                    $jurusan = $jurusanMap[$lower];

                    break;
                }
            }
        } else {
            $tingkat = 10;
            $jurusan = 'IPA';
        }

        return Kelas::create([
            'nama' => $namaKelas,
            'tingkat' => $tingkat,
            'jurusan' => $jurusan,
        ]);
    }
}
