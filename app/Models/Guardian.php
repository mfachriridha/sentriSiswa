<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'name', 'job', 'phone', 'address'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}