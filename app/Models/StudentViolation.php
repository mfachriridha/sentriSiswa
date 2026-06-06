<?php

namespace App\Models;

use Database\Factories\StudentViolationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['student_profile_id', 'violation_type_id', 'recorded_by_user_id', 'violation_date', 'violation_name', 'violation_category', 'point_deduction', 'notes', 'status', 'approved_by_user_id', 'approved_at', 'rejection_reason'])]
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

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            'pending' => 'Menunggu ACC',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'violation_date' => 'date',
            'point_deduction' => 'integer',
            'approved_at' => 'datetime',
        ];
    }
}
