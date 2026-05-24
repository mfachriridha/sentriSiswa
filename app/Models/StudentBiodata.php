<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentBiodata extends Model
{
    protected $table = 'student_biodata';

    protected $fillable = [
        'student_profile_id',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'religion',
        'family_status',
        'child_number',
        'school_of_origin',
        'admission_date',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'parent_address',
        'parent_phone',
        'guardian_name',
        'guardian_occupation',
        'guardian_address',
        'guardian_phone',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'child_number' => 'integer',
    ];

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function isComplete(): bool
    {
        return filled($this->place_of_birth)
            && filled($this->date_of_birth)
            && filled($this->gender)
            && filled($this->religion);
    }
}
