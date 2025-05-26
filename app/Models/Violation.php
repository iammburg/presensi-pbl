<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'violation_points_id',
        'violation_date',
        'academic_year_id',
        'description',
        'penalty',
        'reported_by',
        'evidence',
        'status',
        // Tambahan validasi
        'validation_status',
        'validator_id',
        'validation_notes',
        'validated_at',
    ];

    protected $casts = [
        'violation_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'nisn');
    }

    public function violationPoint()
    {
        return $this->belongsTo(ViolationPoint::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'reported_by', 'nip');
    }

    public function validator()
    {
        return $this->belongsTo(Teacher::class, 'validator_id');
    }
}
