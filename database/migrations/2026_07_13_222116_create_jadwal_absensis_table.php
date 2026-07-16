<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal_absensis', function (Blueprint $table) {
            $table->id();
            $table->string('hari')->unique(); // Senin, Selasa, Rabu, Kamis, Jumat
            $table->time('absen_datang_buka')->default('06:00:00');
            $table->time('batas_waktu_terlambat')->default('07:15:00');
            $table->time('absen_datang_tutup')->default('08:00:00');
            $table->time('absen_pulang_buka')->default('15:00:00');
            $table->time('batas_pulang_cepat')->default('15:30:00');
            $table->time('absen_pulang_tutup')->default('17:00:00');
            $table->boolean('is_libur')->default(false);
            $table->timestamps();
        });

        // Insert default data for Senin - Jumat
        $haris = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        foreach ($haris as $hari) {
            DB::table('jadwal_absensis')->insert([
                'hari' => $hari,
                'absen_datang_buka' => '06:00:00',
                'batas_waktu_terlambat' => '07:15:00',
                'absen_datang_tutup' => '08:00:00',
                'absen_pulang_buka' => '15:00:00',
                'batas_pulang_cepat' => '15:30:00',
                'absen_pulang_tutup' => '17:00:00',
                'is_libur' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_absensis');
    }
};
