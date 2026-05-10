<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentParent extends Model
{
    use HasFactory;

    protected $table = 'student_parents';

    protected $fillable = ['student_id', 'type', 'name', 'job', 'phone'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}