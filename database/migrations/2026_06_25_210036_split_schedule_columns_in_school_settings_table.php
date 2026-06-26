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
            $table->dropColumn(['absensi_buka_waktu', 'absensi_tutup_waktu']);
            
            $table->time('absen_datang_buka')->default('06:00:00')->after('nama_sekolah');
            $table->time('absen_datang_tutup')->default('08:00:00')->after('absen_datang_buka');
            $table->time('absen_pulang_buka')->default('15:00:00')->after('absen_datang_tutup');
            $table->time('absen_pulang_tutup')->default('17:00:00')->after('absen_pulang_buka');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->time('absensi_buka_waktu')->default('07:00:00')->after('nama_sekolah');
            $table->time('absensi_tutup_waktu')->default('16:00:00')->after('absensi_buka_waktu');
            
            $table->dropColumn([
                'absen_datang_buka',
                'absen_datang_tutup',
                'absen_pulang_buka',
                'absen_pulang_tutup'
            ]);
        });
    }
};
