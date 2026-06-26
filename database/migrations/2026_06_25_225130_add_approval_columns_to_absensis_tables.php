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
        if (Schema::hasTable('absensis')) {
            Schema::table('absensis', function (Blueprint $table) {
                if (!Schema::hasColumn('absensis', 'status_pengajuan')) {
                    $table->enum('status_pengajuan', ['pending', 'approved', 'rejected'])->nullable()->after('status');
                }
                if (!Schema::hasColumn('absensis', 'is_notified')) {
                    $table->boolean('is_notified')->default(true)->after('status_pengajuan');
                }
            });
        }

        if (Schema::hasTable('absensi_guru')) {
            Schema::table('absensi_guru', function (Blueprint $table) {
                if (!Schema::hasColumn('absensi_guru', 'status_pengajuan')) {
                    $table->enum('status_pengajuan', ['pending', 'approved', 'rejected'])->nullable()->after('status');
                }
                if (!Schema::hasColumn('absensi_guru', 'is_notified')) {
                    $table->boolean('is_notified')->default(true)->after('status_pengajuan');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('absensis')) {
            Schema::table('absensis', function (Blueprint $table) {
                $table->dropColumn(['status_pengajuan', 'is_notified']);
            });
        }

        if (Schema::hasTable('absensi_guru')) {
            Schema::table('absensi_guru', function (Blueprint $table) {
                $table->dropColumn(['status_pengajuan', 'is_notified']);
            });
        }
    }
};
