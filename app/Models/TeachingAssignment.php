<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeachingAssignment extends Model
{
    use HasFactory;

    protected $table = 'teaching_assignments';

    protected $fillable = [
        'academic_year_id',
        'subject_id',
        'teacher_id',
        'class_id',
    ];


    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'nip');
    }

    /**
     * Get the subject that belongs to this teaching assignment.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }


    /**
     * Get the class that belongs to this teaching assignment.
     */
    public function schoolClass()
    {

    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'nip');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }


    /**
     * Get the academic year that belongs to this teaching assignment.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }


    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class, 'assignment_id');
    }

}
