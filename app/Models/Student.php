<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'nisn';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nis',
        'nisn',
        'enter_year',
        'user_id',
        'name',
        'gender',
        'birth_date',
        'address',
        'phone',
        'parent_name',
        'parent_phone',
        'parent_email',
        'photo',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function classAssignments()
    {
        // pivot table student_class_assignments.student_id menyimpan nisn
        return $this->hasMany(StudentClassAssignment::class,);
    }
     public function latestClassAssignment()
    {
        return $this->hasOne(StudentClassAssignment::class)
            ->latest(); // Ambil yang paling baru berdasarkan created_at
    }
    public function schoolClass()
    {
        return $this->belongsToMany(SchoolClass::class, 'student_class_assignments', 'student_id', 'class_id')
            ->withPivot('academic_year_id')
            ->orderByDesc('student_class_assignments.created_at')
            ->limit(1);
    }

    /**
     * Override agar route model binding pakai 'nisn' sebagai key.
     */
    public function getRouteKeyName()
    {
        return 'nisn';
    }
}
