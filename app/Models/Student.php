<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis', 'nisn', 'name', 'gender', 'kelas_id',
        'phone', 'parent_phone',
        'birth_place', 'birth_date', 'religion', 'family_status', 'birth_order',
        'address', 'home_phone', 'prev_school', 'admission_class', 'admission_date',
        'father_name', 'father_job', 'father_phone',
        'mother_name', 'mother_job', 'mother_phone',
        'parent_address',
        'guardian_name', 'guardian_job', 'guardian_phone', 'guardian_address',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}