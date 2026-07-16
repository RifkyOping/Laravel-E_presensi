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
        Schema::table('absensi_guru', function (Blueprint $table) {
            $table->string('judul_pengajuan')->nullable()->after('kategori');
        });

        // Modifikasi enum status
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE absensi_guru MODIFY COLUMN status ENUM('hadir','izin','sakit','alpa','cuti','tugas') NOT NULL DEFAULT 'hadir'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_guru', function (Blueprint $table) {
            $table->dropColumn('judul_pengajuan');
        });

        // Kembalikan enum status ke aslinya
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE absensi_guru MODIFY COLUMN status ENUM('hadir','izin','sakit','alpa') NOT NULL DEFAULT 'hadir'");
    }
};
