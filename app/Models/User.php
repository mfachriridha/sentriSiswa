<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'status', 'photo', 'whatsapp_number'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'status' => 'string',
        ];
    }

    public function isRegistered(): bool
    {
        return $this->status === 'registered';
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function guru(): HasOne
    {
        return $this->hasOne(Guru::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuru(): bool
    {
        return in_array($this->role, ['wali_kelas', 'bk', 'kesiswaan'], true);
    }

    public function isWaliKelas(): bool
    {
        return $this->role === 'wali_kelas';
    }

    public function isBk(): bool
    {
        return $this->role === 'bk';
    }

    public function isKesiswaan(): bool
    {
        return $this->role === 'kesiswaan';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function isTeacher(): bool
    {
        return $this->isGuru();
    }

    public function isHomeroom(): bool
    {
        return $this->isWaliKelas();
    }

    public function isCounselor(): bool
    {
        return $this->isBk();
    }

    public function isStudentAffairs(): bool
    {
        return $this->isKesiswaan();
    }

    public function isStudent(): bool
    {
        return $this->isSiswa();
    }

    public function dashboardRouteName(): string
    {
        return match ($this->role) {
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
        return match ($this->role) {
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
        $prefix = match ($this->role) {
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
            default => "{$prefix}.profil",
        };
    }

    public function homeroomClass(): HasOne
    {
        return $this->hasOne(SchoolClass::class, 'homeroom_teacher_id');
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class);
    }
}
