<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'teachers';
    protected $primaryKey = 'nip';
    public $incrementing = false;
    protected $keyType = 'unsignedInteger';
    protected $fillable = [
        'nip',
        'name',
        'phone',
        'address',
        'gender',
        'birth_date',
        'photo',
        'user_id',
    ];
}
