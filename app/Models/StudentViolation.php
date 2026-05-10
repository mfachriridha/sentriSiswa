<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentViolation extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'violation_id', 'note'];

    public function student() { return $this->belongsTo(Student::class); }
    public function violation() { return $this->belongsTo(Violation::class); }
}