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
        Schema::table('school_settings', function (Blueprint $table) {
            $table->time('batas_waktu_terlambat')->default('07:15:00')->after('absen_datang_tutup');
            $table->time('batas_pulang_cepat')->default('15:00:00')->after('absen_pulang_buka');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['batas_waktu_terlambat', 'batas_pulang_cepat']);
        });
    }
};
