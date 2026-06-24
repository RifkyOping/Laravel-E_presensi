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
        Schema::create('e_book_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('e_book_id')->constrained()->cascadeOnDelete();
            $table->text('pertanyaan');
            $table->json('opsi_jawaban'); // array of options e.g. ["A", "B", "C", "D"]
            $table->string('kunci_jawaban'); // the correct option
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_book_questions');
    }
};
