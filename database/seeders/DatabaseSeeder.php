<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Pengguna::factory()->admin()->create([
            'nama' => 'Admin',
            'email' => 'admin@sentrisiswa.test',
            'password' => Hash::make('password123'),
            'status' => 'registered',
        ]);

        $this->call([
            ViolationTypeSeeder::class,
            KategoriPengajuanPoinSeeder::class,
            Ipa2Seeder::class,
        ]);
    }

    // /** Satu akun BK per tingkat, sesuai lingkup pantauannya. */
    // private function buatAkunBk(): void
    // {
    //     $tingkatan = [
    //         '10' => ['nama' => 'Bk Sepuluh', 'nip' => '19100000000001'],
    //         '11' => ['nama' => 'Bk Sebelas', 'nip' => '19110000000001'],
    //         '12' => ['nama' => 'Bk Dua Belas', 'nip' => '19120000000001'],
    //     ];

    //     foreach ($tingkatan as $tingkat => $info) {
    //         // Kunci array '10'/'11'/'12' otomatis di-cast PHP jadi integer array
    //         // key - kalau gak dibalikin ke string, MySQL nganggep integer itu
    //         // INDEX enum (bukan value-nya), bukan '10'/'11'/'12' yang dimaksud.
    //         $tingkat = (string) $tingkat;

    //         $guru = Pengguna::firstOrCreate(
    //             ['email' => "kls{$tingkat}bk@sentrisiswa.test"],
    //             [
    //                 'nama' => $info['nama'],
    //                 'password' => Hash::make('password123'),
    //                 'peran' => 'bk',
    //                 'status' => 'registered',
    //             ]
    //         );

    //         $guru->profilGuru()->firstOrCreate(
    //             ['pengguna_id' => $guru->id],
    //             [
    //                 'nip' => $info['nip'],
    //                 'tipe_guru' => 'bk',
    //                 'tingkat' => $tingkat,
    //             ]
    //         );
    //     }
    // }

    // private function buatAkunKesiswaan(): void
    // {
    //     $guru = Pengguna::firstOrCreate(
    //         ['email' => 'kesiswaan@sentrisiswa.test'],
    //         [
    //             'nama' => 'Kesiswaan',
    //             'password' => Hash::make('password123'),
    //             'peran' => 'kesiswaan',
    //             'status' => 'registered',
    //         ]
    //     );

    //     $guru->profilGuru()->firstOrCreate(
    //         ['pengguna_id' => $guru->id],
    //         [
    //             'nip' => '19990000000001',
    //             'tipe_guru' => 'kesiswaan',
    //         ]
    //     );
    // }
}
