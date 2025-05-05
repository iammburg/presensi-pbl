<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    protected $primaryKey = 'nisn';
    public $incrementing = false;
    protected $keyType = 'unsignedInteger';
    protected $fillable = [
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
}

    // public function class()
    // {
    //     return $this->belongsTo(SchoolClass::class, 'class_id');
    // }
}
