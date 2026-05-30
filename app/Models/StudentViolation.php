<?php

namespace App\Models;

use Database\Factories\StudentViolationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['student_profile_id', 'violation_type_id', 'recorded_by_user_id', 'violation_date', 'violation_name', 'violation_category', 'point_deduction', 'notes'])]
class StudentViolation extends Model
{
    /** @use HasFactory<StudentViolationFactory> */
    use HasFactory;

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function violationType(): BelongsTo
    {
        return $this->belongsTo(ViolationType::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'violation_date' => 'date',
            'point_deduction' => 'integer',
        ];
    }
}
