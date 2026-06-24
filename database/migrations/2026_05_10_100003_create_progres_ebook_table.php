<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progres_ebook', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('e_book_id')->constrained('e_books')->onDelete('cascade');
            $table->boolean('selesai')->default(false);
            $table->decimal('skor_suara', 5, 2)->nullable()->comment('Persentase kesamaan suara 0-100');
            $table->timestamp('selesai_pada')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'e_book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_ebook');
    }
};
