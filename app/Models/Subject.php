<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = ['code', 'name', 'teacher_id', 'description'];

    /**
     * Relasi ke model Teacher
     * Setiap mata pelajaran memiliki satu guru pengampu.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}