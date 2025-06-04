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

    /**
     * Semua assignment (history) siswa ini
     */
    public function classAssignments()
    {
        return $this->hasMany(StudentClassAssignment::class, 'student_id');
    }

    /**
     * Assignment terbaru (latest) per siswa, plus eager schoolClass
     */
    public function currentAssignment()
    {
        return $this->hasOne(StudentClassAssignment::class, 'student_id')
                    ->latestOfMany()
                    ->with('schoolClass');
    }

    /**
     * Relasi langsung ke kelas aktif siswa (SchoolClass)
     */
    public function schoolClass()
    {
        // Ambil dari currentAssignment jika ada, relasi ke SchoolClass
        return $this->currentAssignment ? $this->currentAssignment->schoolClass() : null;
    }

    /**
     * Override agar route model binding pakai 'nisn' sebagai key.
     */
    public function getRouteKeyName()
    {
        return 'nisn';
    }
}
