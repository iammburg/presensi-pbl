<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $table = 'academic_years';
    
    protected $fillable = [
        'start_year',
        'end_year',
        'semester',
        'is_active',
    ];

    /**
     * Accessor untuk label tahun ajaran
     */
    public function getYearLabelAttribute()
    {
        return $this->start_year . '/' . $this->end_year . ' - Semester ' . $this->semester;
    }

    /**
     * Relasi ke homeroom_assignments
     */
    public function homeroomAssignments()
{
    return $this->hasMany(HomeroomAssignment::class);
}

}
