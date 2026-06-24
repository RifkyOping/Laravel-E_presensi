<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update enum pada kolom metode
        DB::statement("ALTER TABLE absensi_mengajar MODIFY COLUMN metode ENUM('daring', 'luring', 'diskusi', 'praktik', 'lainnya') NOT NULL DEFAULT 'luring'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE absensi_mengajar MODIFY COLUMN metode ENUM('ceramah', 'diskusi', 'praktik', 'daring', 'lainnya') NOT NULL DEFAULT 'ceramah'");
    }
};
