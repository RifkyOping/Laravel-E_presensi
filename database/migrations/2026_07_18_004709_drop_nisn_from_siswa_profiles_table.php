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
        Schema::table('siswa_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('siswa_profiles', 'nisn')) {
                $table->dropColumn('nisn');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('siswa_profiles', 'nisn')) {
                $table->string('nisn', 20)->nullable()->unique()->after('id');
            }
        });
    }
};
