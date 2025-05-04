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
}
