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
        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->string('rpp_periode', 7)->nullable()->after('rpp_status')->comment('Format: YYYY-MM');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->dropColumn('rpp_periode');
        });
    }
};
