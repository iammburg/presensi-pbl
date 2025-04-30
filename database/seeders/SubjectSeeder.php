<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tambahkan data mata pelajaran ke tabel subjects
        Subject::create(['code' => 'MAT12', 'name' => 'Matematika Lanjutan', 'teacher_id' => 1, 'description' => 'Opsional']);
        Subject::create(['code' => 'FIS11', 'name' => 'Fisika Dasar', 'teacher_id' => 2, 'description' => 'Opsional']);
        Subject::create(['code' => 'KIM10', 'name' => 'Kimia Dasar', 'teacher_id' => 3, 'description' => 'Opsional']);
        Subject::create(['code' => 'BIO10', 'name' => 'Biologi Dasar', 'teacher_id' => 4, 'description' => 'Opsional']);
        Subject::create(['code' => 'ENG10', 'name' => 'Bahasa Inggris', 'teacher_id' => 5, 'description' => 'Opsional']);
    }
}