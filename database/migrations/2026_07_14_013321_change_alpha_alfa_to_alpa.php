<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Expand enum di tabel absensis (absensi siswa)
        DB::statement("ALTER TABLE absensis MODIFY status ENUM('hadir','izin','sakit','alpha','alpa') NOT NULL DEFAULT 'hadir'");
        DB::statement("UPDATE absensis SET status = 'alpa' WHERE status = 'alpha'");
        DB::statement("ALTER TABLE absensis MODIFY status ENUM('hadir','izin','sakit','alpa') NOT NULL DEFAULT 'hadir'");

        // 2. Expand enum di tabel absensi_guru
        DB::statement("ALTER TABLE absensi_guru MODIFY status ENUM('hadir','izin','sakit','alpha','alpa') NOT NULL DEFAULT 'hadir'");
        DB::statement("UPDATE absensi_guru SET status = 'alpa' WHERE status = 'alpha'");
        DB::statement("ALTER TABLE absensi_guru MODIFY status ENUM('hadir','izin','sakit','alpa') NOT NULL DEFAULT 'hadir'");

        // 3. Update tabel absensi_kelas_siswa: 'alfa' -> 'alpa'
        if (Schema::hasTable('absensi_kelas_siswa')) {
            DB::statement("UPDATE absensi_kelas_siswa SET status = 'alpa' WHERE status = 'alfa' OR status = 'alpha'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE absensis MODIFY status ENUM('hadir','izin','sakit','alpha','alpa') NOT NULL DEFAULT 'hadir'");
        DB::statement("UPDATE absensis SET status = 'alpha' WHERE status = 'alpa'");
        DB::statement("ALTER TABLE absensis MODIFY status ENUM('hadir','izin','sakit','alpha') NOT NULL DEFAULT 'hadir'");

        DB::statement("ALTER TABLE absensi_guru MODIFY status ENUM('hadir','izin','sakit','alpha','alpa') NOT NULL DEFAULT 'hadir'");
        DB::statement("UPDATE absensi_guru SET status = 'alpha' WHERE status = 'alpa'");
        DB::statement("ALTER TABLE absensi_guru MODIFY status ENUM('hadir','izin','sakit','alpha') NOT NULL DEFAULT 'hadir'");

        if (Schema::hasTable('absensi_kelas_siswa')) {
            DB::statement("UPDATE absensi_kelas_siswa SET status = 'alfa' WHERE status = 'alpa'");
        }
    }
};
