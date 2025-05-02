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

    public function getYearLabelAttribute()
    {
        return $this->start_year . '/' . $this->end_year . ' - Semester ' . $this->semester;
    }

    public function classes() {
        return $this->hasMany(ClassModel::class, 'academic_year_id');
    }

}
