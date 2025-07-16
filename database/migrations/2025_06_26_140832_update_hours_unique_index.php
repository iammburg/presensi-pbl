<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// database/migrations/xxxx_xx_xx_update_hours_unique_index.php
return new class extends Migration {
    public function up()
    {
        // Cek apakah unique constraint 'hours_slot_number_unique' ada
        $uniqueExists = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.statistics
            WHERE table_schema = ?
            AND table_name = 'hours'
            AND index_name = 'hours_slot_number_unique'
        ", [config('database.connections.mysql.database')]);

        if ($uniqueExists[0]->count > 0) {
            Schema::table('hours', function (Blueprint $table) {
                $table->dropUnique(['slot_number']);
            });
        }

        // Cek apakah unique constraint gabungan sudah ada
        $compositeExists = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.statistics
            WHERE table_schema = ?
            AND table_name = 'hours'
            AND index_name = 'hours_slot_number_is_friday_unique'
        ", [config('database.connections.mysql.database')]);

        if ($compositeExists[0]->count == 0) {
            Schema::table('hours', function (Blueprint $table) {
                $table->unique(['slot_number', 'is_friday']);
            });
        }
    }

    public function down()
    {
        // Cek apakah unique constraint gabungan ada
        $compositeExists = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.statistics
            WHERE table_schema = ?
            AND table_name = 'hours'
            AND index_name = 'hours_slot_number_is_friday_unique'
        ", [config('database.connections.mysql.database')]);

        if ($compositeExists[0]->count > 0) {
            Schema::table('hours', function (Blueprint $table) {
                $table->dropUnique(['slot_number', 'is_friday']);
            });
        }

        // Cek apakah unique constraint tunggal sudah ada
        $uniqueExists = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.statistics
            WHERE table_schema = ?
            AND table_name = 'hours'
            AND index_name = 'hours_slot_number_unique'
        ", [config('database.connections.mysql.database')]);

        if ($uniqueExists[0]->count == 0) {
            Schema::table('hours', function (Blueprint $table) {
                $table->unique(['slot_number']);
            });
        }
    }
};
