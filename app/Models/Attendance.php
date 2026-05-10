<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id', 'date', 'status', 'selfie_path',
        'latitude', 'longitude', 'is_inside_geofence',
        'confirmed_by', 'confirmed_at', 'note',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_inside_geofence' => 'boolean',
            'confirmed_at' => 'datetime',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
