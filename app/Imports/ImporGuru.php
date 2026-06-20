<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImporGuru implements ToCollection, WithChunkReading, WithHeadingRow
{
    public int $guruDibuat = 0;

    public int $guruDilewati = 0;

    public int $kelasDibuat = 0;

    public int $error = 0;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $detailError = [];

    protected string $passwordDefault;

    /**
     * @var array<string, int>
     */
    protected array $cacheKelas = [];

    protected int $indexBaris = 0;

    public function __construct()
    {
        $this->passwordDefault = Hash::make('password');
    }

    public function collection(Collection $rows): void
    {
        $penggunaBaru = [];
        $barisGuru = [];

        foreach ($rows as $row) {
            $this->indexBaris++;
            $nama = trim((string) ($row['nama'] ?? ''));
            $email = isset($row['email']) ? trim((string) $row['email']) : null;
            $peranRaw = trim((string) ($row['peran'] ?? ''));

            if (empty($nama)) {
                $this->error++;
                $this->detailError[] = ['baris' => $this->indexBaris, 'nama' => '(kosong)', 'alasan' => 'Nama kosong'];

                continue;
            }

            $peran = match (true) {
                in_array(strtolower($peranRaw), ['bk', 'bimbingan konseling']) => User::PERAN_BK,
                in_array(strtolower($peranRaw), ['kesiswaan']) => User::PERAN_KESISWAAN,
                default => User::PERAN_WALI_KELAS,
            };

            $penggunaBaru[] = [
                'nama' => $nama,
                'email' => $email,
                'peran' => $peran,
                'status' => User::STATUS_TERDAFTAR,
                'password' => $this->passwordDefault,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $barisGuru[] = [
                'nama' => $nama,
                'peran' => $peran,
                'baris' => $this->indexBaris,
            ];
        }

        if (empty($penggunaBaru)) {
            return;
        }

        DB::transaction(function () use ($penggunaBaru, $barisGuru): void {
            $existingEmails = collect($penggunaBaru)->pluck('email')->filter()->toArray();

            $existingUsers = [];
            if (! empty($existingEmails)) {
                $existingUsers = User::whereIn('email', $existingEmails)->pluck('id', 'email')->toArray();
            }

            $penggunaFresh = [];
            $countExist = 0;

            foreach ($penggunaBaru as $i => $pengguna) {
                $email = $pengguna['email'];

                if ($email && isset($existingUsers[$email])) {
                    $countExist++;
                    $this->detailError[] = [
                        'baris' => $barisGuru[$i]['baris'],
                        'nama' => $pengguna['nama'],
                        'alasan' => 'Email sudah terdaftar ('.$email.')',
                    ];
                } else {
                    $penggunaFresh[] = $pengguna;
                }
            }

            foreach (array_chunk($penggunaFresh, 500) as $chunk) {
                User::insert($chunk);
            }

            $this->guruDibuat += count($penggunaFresh);
            $this->guruDilewati += $countExist;
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
