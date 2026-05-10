<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherClass extends Model
{
    protected $fillable = ['user_id', 'school_class_id', 'role'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }
}
