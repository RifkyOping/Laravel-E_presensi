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
        Schema::create('buku_manuals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('level')->default(1);
            $table->string('judul');
            $table->string('penulis');
            $table->string('penerbit');
            $table->string('kota_terbit');
            $table->string('tahun_terbit');
            $table->integer('jumlah_halaman');
            $table->string('foto_sampul');
            $table->boolean('status_selesai')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_manuals');
    }
};
