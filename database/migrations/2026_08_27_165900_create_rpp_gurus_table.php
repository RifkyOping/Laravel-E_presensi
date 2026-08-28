<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel rpp_gurus
        Schema::create('rpp_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tingkat');       // X, XI, XII
            $table->string('jurusan');       // RPL, TKJ, dll
            $table->string('rpp_file');
            $table->string('rpp_status')->default('pending'); // pending, disetujui, ditolak
            $table->string('rpp_periode');   // Format: 2026-08
            $table->text('rpp_pesan')->nullable();
            $table->timestamps();

            // 1 guru hanya bisa punya 1 RPP aktif per tingkat+jurusan+periode
            $table->unique(['user_id', 'tingkat', 'jurusan', 'rpp_periode'], 'rpp_guru_unik');
        });

        // 2. Hapus kolom RPP lama dari guru_profiles
        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->dropColumn(['rpp_file', 'rpp_status', 'rpp_periode', 'rpp_pesan']);
        });
    }

    public function down(): void
    {
        // Kembalikan kolom RPP ke guru_profiles
        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->string('rpp_file')->nullable();
            $table->string('rpp_status')->nullable();
            $table->string('rpp_periode')->nullable();
            $table->text('rpp_pesan')->nullable();
        });

        Schema::dropIfExists('rpp_gurus');
    }
};
