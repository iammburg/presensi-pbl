<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $fillable = [
        'name',
        'parallel_name',
        'academic_year_id',
        'is_active',
    ];

    // app/Models/SchoolClass.php

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

}
