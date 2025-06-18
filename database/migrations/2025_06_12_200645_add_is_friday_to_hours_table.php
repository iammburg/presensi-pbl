<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hours', function (Blueprint $table) {
            $table->boolean('is_friday')->default(false)->after('slot_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hours', function (Blueprint $table) {
            $table->dropColumn('is_friday');
        });
    }
};
