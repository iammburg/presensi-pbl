<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hour extends Model
{
    protected $fillable = [
        'session_type','slot_number','is_friday',
        'start_time','end_time'
    ];

    protected $casts = [
        'is_friday' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
    ];
}