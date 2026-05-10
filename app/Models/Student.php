<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis', 'nisn', 'name', 'gender', 'kelas_id', 'poin',
        'birth_place', 'birth_date', 'religion', 'family_status', 'birth_order',
        'address', 'home_phone', 'prev_school', 'admission_class', 'admission_date',
    ];

    public function schoolClass() { return $this->belongsTo(SchoolClass::class); }
    public function parents() { return $this->hasMany(StudentParent::class); }
    public function father() { return $this->hasOne(StudentParent::class)->where('type', 'father'); }
    public function mother() { return $this->hasOne(StudentParent::class)->where('type', 'mother'); }
    public function guardian() { return $this->hasOne(Guardian::class); }
    public function violations() { return $this->hasMany(StudentViolation::class); }
}