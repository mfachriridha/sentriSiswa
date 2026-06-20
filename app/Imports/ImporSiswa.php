<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImporSiswa implements ToCollection, WithChunkReading, WithHeadingRow
{
    public int $siswaDibuat = 0;

    public int $siswaDilewati = 0;

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
        $barisSiswa = [];

        foreach ($rows as $row) {
            $this->indexBaris++;
            $nama = trim((string) ($row['nama'] ?? ''));
            $nisRaw = isset($row['nis']) ? trim((string) $row['nis']) : null;
            $nis = $nisRaw ? mb_substr($nisRaw, 0, 20) : null;
            $nisnRaw = isset($row['nisn']) ? trim((string) $row['nisn']) : null;
            $nisn = $nisnRaw ? mb_substr((string) $nisnRaw, 0, 10) : null;
            $kelasNama = isset($row['kelas']) ? trim((string) $row['kelas']) : null;

            if (empty($nama)) {
                $this->error++;
                $this->detailError[] = ['baris' => $this->indexBaris, 'nama' => '(kosong)', 'alasan' => 'Nama kosong'];

                continue;
            }

            $kelasId = null;
            if ($kelasNama) {
                if (! isset($this->cacheKelas[$kelasNama])) {
                    $tingkat = (int) strtok($kelasNama, ' .-');
                    $kelas = Kelas::firstOrCreate(
                        ['nama' => $kelasNama],
                        ['tingkat' => in_array($tingkat, [10, 11, 12]) ? (string) $tingkat : '10']
                    );
                    $this->cacheKelas[$kelasNama] = $kelas->id;
                }
                $kelasId = $this->cacheKelas[$kelasNama];
            }

            $penggunaBaru[] = [
                'nama' => $nama,
                'peran' => User::PERAN_SISWA,
                'status' => User::STATUS_BELUM_TERDAFTAR,
                'password' => $this->passwordDefault,
                'email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $barisSiswa[] = [
                'nama' => $nama,
                'nisn' => $nisn,
                'nis' => $nis,
                'kelas_id' => $kelasId,
                'baris' => $this->indexBaris,
            ];
        }

        if (empty($penggunaBaru)) {
            return;
        }

        DB::transaction(function () use ($penggunaBaru, $barisSiswa): void {
            $nisnList = collect($barisSiswa)->pluck('nisn')->filter()->toArray();

            $siswaExist = [];
            if (! empty($nisnList)) {
                $siswaExist = Siswa::whereIn('nisn', $nisnList)->pluck('pengguna_id', 'nisn')->toArray();
            }

            $penggunaFresh = [];
            $dataSiswa = [];
            $countExist = 0;

            foreach ($barisSiswa as $i => $siswa) {
                $nisn = $siswa['nisn'];

                if ($nisn && isset($siswaExist[$nisn])) {
                    $countExist++;
                    $this->detailError[] = [
                        'baris' => $siswa['baris'],
                        'nama' => $siswa['nama'],
                        'alasan' => 'NISN sudah ada di database ('.$nisn.')',
                    ];
                    $dataSiswa[] = [
                        'pengguna_id' => $siswaExist[$nisn],
                        'nisn' => $nisn,
                        'nis' => $siswa['nis'],
                        'kelas_id' => $siswa['kelas_id'],
                        'updated_at' => now(),
                    ];
                } else {
                    $penggunaFresh[] = $penggunaBaru[$i];
                    $dataSiswa[] = [
                        'pengguna_id' => null,
                        'nisn' => $nisn,
                        'nis' => $siswa['nis'],
                        'kelas_id' => $siswa['kelas_id'],
                    ];
                }
            }

            foreach (array_chunk($penggunaFresh, 500) as $chunk) {
                User::insert($chunk);
            }

            $this->siswaDibuat += count($penggunaFresh);
            $this->siswaDilewati += $countExist;

            if (count($penggunaFresh) > 0) {
                $firstId = User::where('peran', User::PERAN_SISWA)->latest('id')->first()->id;
                $offset = $firstId - count($penggunaFresh) + 1;
            } else {
                $offset = 0;
            }

            $inserts = [];
            $newIdx = 0;

            foreach ($dataSiswa as $siswa) {
                if ($siswa['pengguna_id'] === null) {
                    $newIdx++;

                    if ($newIdx > count($penggunaFresh)) {
                        continue;
                    }

                    $inserts[] = [
                        'pengguna_id' => $offset + $newIdx - 1,
                        'nisn' => $siswa['nisn'],
                        'nis' => $siswa['nis'],
                        'kelas_id' => $siswa['kelas_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    $inserts[] = [
                        'pengguna_id' => $siswa['pengguna_id'],
                        'nisn' => $siswa['nisn'],
                        'nis' => $siswa['nis'],
                        'kelas_id' => $siswa['kelas_id'],
                        'updated_at' => now(),
                    ];
                }
            }

            foreach (array_chunk($inserts, 500) as $chunk) {
                Siswa::upsert($chunk, ['pengguna_id'], ['nisn', 'nis', 'kelas_id', 'updated_at']);
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
