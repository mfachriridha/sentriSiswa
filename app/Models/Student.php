<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nis', 'nisn', 'name', 'gender', 'school_class_id', 'poin',
        'birth_place', 'birth_date', 'religion', 'family_status', 'birth_order',
        'address', 'home_phone', 'prev_school', 'admission_class', 'admission_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function parents()
    {
        return $this->hasMany(StudentParent::class);
    }

    public function father()
    {
        return $this->hasOne(StudentParent::class)->where('type', 'father');
    }

    public function mother()
    {
        return $this->hasOne(StudentParent::class)->where('type', 'mother');
    }

    public function guardian()
    {
        return $this->hasOne(Guardian::class);
    }

    public function violations()
    {
        return $this->hasMany(StudentViolation::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
