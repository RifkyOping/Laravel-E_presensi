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
        Schema::table('absensi_mengajar', function (Blueprint $table) {
            $table->time('waktu_absen_masuk')->nullable()->after('jam_selesai');
            $table->time('waktu_absen_keluar')->nullable()->after('waktu_absen_masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_mengajar', function (Blueprint $table) {
            $table->dropColumn(['waktu_absen_masuk', 'waktu_absen_keluar']);
        });
    }
};
