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
            $table->dropColumn(['materi', 'metode', 'jumlah_siswa_hadir', 'keterangan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_mengajar', function (Blueprint $table) {
            $table->text('materi')->nullable();
            $table->enum('metode', ['ceramah', 'diskusi', 'praktik', 'daring', 'lainnya'])->default('ceramah');
            $table->integer('jumlah_siswa_hadir')->default(0);
            $table->text('keterangan')->nullable();
        });
    }
};
