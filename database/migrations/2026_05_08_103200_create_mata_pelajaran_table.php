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
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                        // Nama mata pelajaran
            $table->string('kode', 20)->unique();          // Kode singkat, misal: MTK, IPA
            $table->enum('tingkat', ['X', 'XI', 'XII', 'Semua'])->default('Semua'); // Tingkat kelas
            $table->string('jurusan')->nullable();         // Jurusan terkait (opsional)
            $table->text('deskripsi')->nullable();         // Deskripsi singkat
            $table->boolean('aktif')->default(true);       // Status aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran');
    }
};
