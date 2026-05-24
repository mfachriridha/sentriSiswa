<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function violations()
    {
        return $this->hasMany(StudentViolation::class);
    }

    public function getPointsAttribute(): int
    {
        $deductions = $this->violations()
            ->join('violation_types', 'student_violations.violation_type_id', '=', 'violation_types.id')
            ->sum('violation_types.point_deduction');

        return max(0, 100 - $deductions);
    }
}
