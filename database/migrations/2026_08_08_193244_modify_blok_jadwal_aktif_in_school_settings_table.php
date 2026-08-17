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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE school_settings MODIFY COLUMN blok_jadwal_aktif ENUM('A', 'B', 'TEFA') DEFAULT 'A'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE school_settings MODIFY COLUMN blok_jadwal_aktif ENUM('A', 'B') DEFAULT 'A'");
    }
};
