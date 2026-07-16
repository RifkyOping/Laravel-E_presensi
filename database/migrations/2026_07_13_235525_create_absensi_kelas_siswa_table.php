<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_kelas_siswa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_mengajar_id');
            $table->unsignedBigInteger('guru_id');
            $table->unsignedBigInteger('siswa_id');
            $table->date('tanggal');
            $table->string('status')->default('hadir'); // hadir, alfa, sakit, izin
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('jadwal_mengajar_id')->references('id')->on('jadwal_mengajars')->onDelete('cascade');
            $table->foreign('guru_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('users')->onDelete('cascade');

            // Satu siswa hanya bisa punya 1 record per jadwal per hari
            $table->unique(['jadwal_mengajar_id', 'siswa_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_kelas_siswa');
    }
};
