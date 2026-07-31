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
        Schema::table('jadwal_mengajars', function (Blueprint $table) {
            $table->enum('tipe_blok', ['A', 'B', 'Semua'])->default('Semua')->after('hari');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_mengajars', function (Blueprint $table) {
            $table->dropColumn('tipe_blok');
        });
    }
};
