<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['nisn', 'nis', 'class_id', 'phone', 'address', 'photo'])]
class StudentProfile extends Model
{
    /** @use HasFactory<StudentProfileFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function biodata(): HasOne
    {
        return $this->hasOne(StudentBiodata::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function studentViolations(): HasMany
    {
        return $this->hasMany(StudentViolation::class);
    }

    public function getPointsAttribute(): int
    {
        $deductions = 0;

        // Jika aggregate sum sudah diload dari query builder (withSum)
        if (array_key_exists('student_violations_sum_point_deduction', $this->attributes)) {
            $deductions = (int) $this->attributes['student_violations_sum_point_deduction'];
        }
        // Jika relasi sudah diload semua (with)
        elseif ($this->relationLoaded('studentViolations')) {
            $deductions = $this->studentViolations->sum('point_deduction');
        }
        // Fallback: query database langsung
        else {
            $deductions = (int) $this->studentViolations()->sum('point_deduction');
        }

        return max(0, 100 - $deductions);
    }
}
