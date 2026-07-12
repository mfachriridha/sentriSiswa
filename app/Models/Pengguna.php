<?php

namespace App\Models;

use Database\Factories\PenggunaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

#[Fillable(['nama', 'email', 'password', 'peran', 'status', 'foto', 'nomor_wa', 'id_google'])]
#[Hidden(['password', 'remember_token'])]
class Pengguna extends Authenticatable
{
    /** @use HasFactory<PenggunaFactory> */
    use HasFactory, Notifiable;

    protected $table = 'pengguna';

    const CREATED_AT = 'dibuat_pada';

    const UPDATED_AT = 'diperbarui_pada';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'string',
        ];
    }

    public function isRegistered(): bool
    {
        return $this->status === 'registered';
    }

    public function hasPassword(): bool
    {
        return $this->password !== null;
    }

    public function punyaGoogle(): bool
    {
        return $this->id_google !== null;
    }

    /**
     * Tautan WhatsApp ke admin sekolah, untuk pengguna yang mentok: identitasnya
     * belum didaftarkan, atau akunnya belum bisa dimasuki.
     *
     * Nomornya diisi admin lewat halaman Profil. Selama masih kosong, tidak ada
     * tautan yang bisa ditawarkan.
     */
    public static function tautanWhatsappAdmin(): ?string
    {
        if (! Schema::hasColumn('pengguna', 'nomor_wa')) {
            return null;
        }

        $nomor = static::where('peran', 'admin')
            ->whereNotNull('nomor_wa')
            ->value('nomor_wa');

        if (! $nomor) {
            return null;
        }

        $bersih = preg_replace('/[^0-9+]/', '', (string) $nomor);
        $bersih = ltrim((string) $bersih, '+');

        if ($bersih === '') {
            return null;
        }

        // Nomor Indonesia biasa ditulis diawali 0; WhatsApp memintanya berkode negara.
        if (str_starts_with($bersih, '0')) {
            $bersih = '62'.substr($bersih, 1);
        }

        return 'https://wa.me/'.$bersih;
    }

    public function needsAdminSetup(): bool
    {
        return $this->isAdmin() && $this->email_verified_at === null;
    }

    public function profilGuru(): HasOne
    {
        return $this->hasOne(ProfilGuru::class, 'pengguna_id');
    }

    public function profilSiswa(): HasOne
    {
        return $this->hasOne(ProfilSiswa::class, 'pengguna_id');
    }

    public function kelasWali(): HasOne
    {
        return $this->hasOne(Kelas::class, 'wali_kelas_id');
    }

    public function isAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    public function isGuru(): bool
    {
        return in_array($this->peran, ['wali_kelas', 'bk', 'kesiswaan'], true);
    }

    public function isWaliKelas(): bool
    {
        return $this->peran === 'wali_kelas';
    }

    public function isBk(): bool
    {
        return $this->peran === 'bk';
    }

    public function isKesiswaan(): bool
    {
        return $this->peran === 'kesiswaan';
    }

    public function isSiswa(): bool
    {
        return $this->peran === 'siswa';
    }

    public function dashboardRouteName(): string
    {
        return match ($this->peran) {
            'admin' => 'admin.dashboard',
            'wali_kelas' => 'wali-kelas.dashboard',
            'bk' => 'bk.dashboard',
            'kesiswaan' => 'kesiswaan.dashboard',
            'siswa' => 'siswa.dashboard',
            default => 'login',
        };
    }

    public function roleLabel(): string
    {
        return match ($this->peran) {
            'admin' => 'Admin',
            'wali_kelas' => 'Wali Kelas',
            'bk' => 'BK',
            'kesiswaan' => 'Kesiswaan',
            'siswa' => 'Siswa',
            default => 'Pengguna',
        };
    }

    public function profilRouteName(string $action = 'show'): string
    {
        $prefix = match ($this->peran) {
            'admin' => 'admin',
            'wali_kelas' => 'wali-kelas',
            'bk' => 'bk',
            'kesiswaan' => 'kesiswaan',
            'siswa' => 'siswa',
            default => 'login',
        };

        if ($prefix === 'login') {
            return 'login';
        }

        return match ($action) {
            'edit' => "{$prefix}.profil.edit",
            'update' => "{$prefix}.profil.update",
            'ganti-sandi' => "{$prefix}.profil.ganti-sandi",
            'set-sandi-baru' => "{$prefix}.profil.set-sandi-baru",
            default => "{$prefix}.profil",
        };
    }
}
