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
        Subject::create(['code' => 'MAT12', 'name' => 'Matematika Lanjutan', 'description' => 'Opsional']);
        Subject::create(['code' => 'FIS11', 'name' => 'Fisika Dasar','description' => 'Opsional']);
        Subject::create(['code' => 'KIM10', 'name' => 'Kimia Dasar', 'description' => 'Opsional']);
        Subject::create(['code' => 'BIO10', 'name' => 'Biologi Dasar', 'description' => 'Opsional']);
        Subject::create(['code' => 'ENG10', 'name' => 'Bahasa Inggris', 'description' => 'Opsional']);
    }
}