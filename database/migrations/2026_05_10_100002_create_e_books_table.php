<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e_books', function (Blueprint $table) {
            $table->id();
            $table->integer('level')->default(1)->comment('Urutan level e-book, mulai dari 1');
            $table->string('judul');
            $table->string('kategori')->nullable();
            $table->text('deskripsi')->nullable();
            $table->longText('konten_teks')->nullable()->comment('Teks referensi untuk pencocokan suara');
            $table->string('file_pdf')->nullable()->comment('Path file PDF di storage');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e_books');
    }
};
