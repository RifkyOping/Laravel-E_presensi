<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            $table->date('tanggal')->after('user_id');
            $table->time('waktu_datang')->nullable()->after('tanggal');
            $table->time('waktu_pulang')->nullable()->after('waktu_datang');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->default('hadir')->after('waktu_pulang');
            $table->text('keterangan')->nullable()->after('status');

            // Satu siswa hanya bisa absen 1x per hari
            $table->unique(['user_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'tanggal', 'waktu_datang', 'waktu_pulang', 'status', 'keterangan']);
        });
    }
};
