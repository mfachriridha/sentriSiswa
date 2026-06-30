<?php

namespace Database\Factories;

use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Pengguna>
 */
class PenggunaFactory extends Factory
{
    protected $model = Pengguna::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'peran' => 'siswa',
            'status' => 'registered',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'peran' => 'admin',
        ]);
    }

    public function homeroom(): static
    {
        return $this->state(fn (array $attributes) => [
            'peran' => 'wali_kelas',
        ]);
    }

    public function counselor(): static
    {
        return $this->state(fn (array $attributes) => [
            'peran' => 'bk',
        ]);
    }

    public function studentAffairs(): static
    {
        return $this->state(fn (array $attributes) => [
            'peran' => 'kesiswaan',
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'peran' => 'siswa',
        ]);
    }
}
