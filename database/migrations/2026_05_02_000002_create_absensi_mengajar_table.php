<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_mengajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->string('mata_pelajaran');
            $table->string('kelas');
            $table->tinyInteger('jam_ke'); // jam pelajaran ke-berapa
            $table->time('jam_mulai');
            $table->time('jam_selesai')->nullable();
            $table->text('materi'); // topik/materi yang diajarkan
            $table->enum('metode', ['ceramah', 'diskusi', 'praktik', 'daring', 'lainnya'])->default('ceramah');
            $table->integer('jumlah_siswa_hadir')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_mengajar');
    }
};
