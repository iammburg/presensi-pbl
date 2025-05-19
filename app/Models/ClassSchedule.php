<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $table = 'class_schedules';
    protected $fillable = [
        'class_id',
        'assignment_id',
        'day_of_week',
        'hour_id',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function assignment()
    {
        return $this->belongsTo(TeachingAssignment::class, 'assignment_id');
    }

    public function hour()
    {
        return $this->belongsTo(Hour::class, 'hour_id');
    }
}
