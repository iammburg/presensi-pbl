<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_update_hours_unique_index.php
return new class extends Migration {
    public function up()
    {
        Schema::table('hours', function (Blueprint $table) {
            // hapus unique tunggal, jika masih ada
            $table->dropUnique(['slot_number']);

            // tambahkan unique gabungan slot_number + is_friday
            $table->unique(['slot_number', 'is_friday']);
        });
    }

    public function down()
    {
        Schema::table('hours', function (Blueprint $table) {
            $table->dropUnique(['slot_number', 'is_friday']);
            $table->unique(['slot_number']);
        });
    }
};