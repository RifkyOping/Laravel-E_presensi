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
            $table->longText('akumulasi_teks')->nullable()->after('selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progres_ebook', function (Blueprint $table) {
            $table->dropColumn('akumulasi_teks');
        });
    }
};
