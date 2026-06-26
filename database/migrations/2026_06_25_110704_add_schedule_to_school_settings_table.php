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
            $table->time('absensi_buka_waktu')->default('07:00:00')->after('nama_sekolah');
            $table->time('absensi_tutup_waktu')->default('16:00:00')->after('absensi_buka_waktu');
            $table->enum('status_absen', ['auto', 'buka', 'tutup'])->default('auto')->after('absensi_tutup_waktu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['absensi_buka_waktu', 'absensi_tutup_waktu', 'status_absen']);
        });
    }
};
