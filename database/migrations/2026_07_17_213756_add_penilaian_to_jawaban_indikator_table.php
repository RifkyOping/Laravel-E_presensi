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
        Schema::table('jawaban_indikators', function (Blueprint $table) {
            $table->tinyInteger('nilai_guru')->nullable()->comment('1:Kurang, 2:Cukup, 3:Baik, 4:Sangat Baik');
            $table->text('catatan_guru')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jawaban_indikators', function (Blueprint $table) {
            $table->dropColumn(['nilai_guru', 'catatan_guru']);
        });
    }
};
