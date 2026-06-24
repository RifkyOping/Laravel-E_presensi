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
        Schema::table('progres_ebook', function (Blueprint $table) {
            $table->boolean('lulus_suara')->default(false)->after('skor_suara');
            $table->boolean('lulus_kuis')->default(false)->after('lulus_suara');
            $table->json('jawaban_kuis')->nullable()->after('lulus_kuis');
            $table->decimal('skor_kuis', 5, 2)->nullable()->after('jawaban_kuis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progres_ebook', function (Blueprint $table) {
            $table->dropColumn(['lulus_suara', 'lulus_kuis', 'jawaban_kuis', 'skor_kuis']);
        });
    }
};
