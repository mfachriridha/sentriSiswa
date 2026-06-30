<?php

namespace Database\Factories;

use App\Models\Pengguna;
use App\Models\TataTertib;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TataTertib>
 */
class TataTertibFactory extends Factory
{
    public function definition(): array
    {
        return [
            'judul' => 'Tata Tertib Sekolah',
            'path_file' => 'tata-tertib/tata-tertib.pdf',
            'dipublikasikan' => true,
            'diunggah_oleh_id' => Pengguna::factory()->homeroom(),
        ];
    }
}
