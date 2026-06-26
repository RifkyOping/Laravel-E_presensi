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
        if (!Schema::hasColumn('absensis', 'file_bukti')) {
            Schema::table('absensis', function (Blueprint $table) {
                $table->string('file_bukti')->nullable()->after('keterangan');
            });
        }

        if (!Schema::hasColumn('absensi_guru', 'file_bukti')) {
            Schema::table('absensi_guru', function (Blueprint $table) {
                $table->string('file_bukti')->nullable()->after('keterangan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn('file_bukti');
        });

        Schema::table('absensi_guru', function (Blueprint $table) {
            $table->dropColumn('file_bukti');
        });
    }
};
