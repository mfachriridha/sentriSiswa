<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nama
 * @property string|null $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string $peran
 * @property string $status
 * @property string|null $foto
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['nama', 'email', 'password', 'peran', 'status', 'foto'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const PERAN_ADMIN = 'admin';

    public const PERAN_WALI_KELAS = 'wali_kelas';

    public const PERAN_BK = 'bk';

    public const PERAN_KESISWAAN = 'kesiswaan';

    public const PERAN_SISWA = 'siswa';

    public const STATUS_BELUM_TERDAFTAR = 'belum_terdaftar';

    public const STATUS_TERDAFTAR = 'terdaftar';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isTerdaftar(): bool
    {
        return $this->status === self::STATUS_TERDAFTAR;
    }

    public function isAdmin(): bool
    {
        return $this->peran === self::PERAN_ADMIN;
    }

    public function isWaliKelas(): bool
    {
        return $this->peran === self::PERAN_WALI_KELAS;
    }

    public function isBk(): bool
    {
        return $this->peran === self::PERAN_BK;
    }

    public function isKesiswaan(): bool
    {
        return $this->peran === self::PERAN_KESISWAAN;
    }

    public function isSiswa(): bool
    {
        return $this->peran === self::PERAN_SISWA;
    }

    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class, 'pengguna_id');
    }

    public function kelasWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }
}
