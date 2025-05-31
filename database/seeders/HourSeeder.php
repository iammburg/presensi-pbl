<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table untuk menghindari duplikasi
        DB::table('hours')->truncate();

        // Data jam untuk Senin-Kamis (id 0-9)
        $weekdaysHours = [
            ['id' => 0, 'session_type' => 'Jam Istirahat', 'slot_number' => 0, 'start_time' => '11:30:00', 'end_time' => '12:15:00'],
            ['id' => 1, 'session_type' => 'Jam Pelajaran', 'slot_number' => 1, 'start_time' => '07:00:00', 'end_time' => '07:45:00'],
            ['id' => 2, 'session_type' => 'Jam Pelajaran', 'slot_number' => 2, 'start_time' => '07:45:00', 'end_time' => '08:30:00'],
            ['id' => 3, 'session_type' => 'Jam Pelajaran', 'slot_number' => 3, 'start_time' => '08:30:00', 'end_time' => '09:15:00'],
            ['id' => 4, 'session_type' => 'Jam Pelajaran', 'slot_number' => 4, 'start_time' => '09:15:00', 'end_time' => '10:00:00'],
            ['id' => 5, 'session_type' => 'Jam Pelajaran', 'slot_number' => 5, 'start_time' => '10:00:00', 'end_time' => '10:45:00'],
            ['id' => 6, 'session_type' => 'Jam Pelajaran', 'slot_number' => 6, 'start_time' => '10:45:00', 'end_time' => '11:30:00'],
            ['id' => 7, 'session_type' => 'Jam Pelajaran', 'slot_number' => 7, 'start_time' => '12:15:00', 'end_time' => '13:00:00'],
            ['id' => 8, 'session_type' => 'Jam Pelajaran', 'slot_number' => 8, 'start_time' => '13:00:00', 'end_time' => '13:45:00'],
            ['id' => 9, 'session_type' => 'Jam Pelajaran', 'slot_number' => 9, 'start_time' => '13:45:00', 'end_time' => '14:30:00'],
        ];

        // Data jam untuk Jumat (id 10-17)
        $fridayHours = [
            ['id' => 10, 'session_type' => 'Jam Istirahat', 'slot_number' => 0, 'start_time' => '09:20:00', 'end_time' => '09:55:00'],
            ['id' => 11, 'session_type' => 'Jam Pelajaran', 'slot_number' => 1, 'start_time' => '07:00:00', 'end_time' => '07:35:00'],
            ['id' => 12, 'session_type' => 'Jam Pelajaran', 'slot_number' => 2, 'start_time' => '07:35:00', 'end_time' => '08:10:00'],
            ['id' => 13, 'session_type' => 'Jam Pelajaran', 'slot_number' => 3, 'start_time' => '08:10:00', 'end_time' => '08:45:00'],
            ['id' => 14, 'session_type' => 'Jam Pelajaran', 'slot_number' => 4, 'start_time' => '08:45:00', 'end_time' => '09:20:00'],
            ['id' => 15, 'session_type' => 'Jam Pelajaran', 'slot_number' => 5, 'start_time' => '09:55:00', 'end_time' => '10:30:00'],
            ['id' => 16, 'session_type' => 'Jam Pelajaran', 'slot_number' => 6, 'start_time' => '10:30:00', 'end_time' => '11:05:00'],
            ['id' => 17, 'session_type' => 'Jam Pelajaran', 'slot_number' => 7, 'start_time' => '11:05:00', 'end_time' => '11:40:00'],
        ];

        // Gabungkan semua data
        $allHours = array_merge($weekdaysHours, $fridayHours);

        // Insert data ke database
        foreach ($allHours as $hour) {
            DB::table('hours')->insert([
                'id' => $hour['id'],
                'session_type' => $hour['session_type'],
                'slot_number' => $hour['slot_number'],
                'start_time' => $hour['start_time'],
                'end_time' => $hour['end_time'],
                'created_at' => null,
                'updated_at' => null,
            ]);
        }

        $this->command->info('Hours table seeded successfully!');
    }
}