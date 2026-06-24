<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_mengajar', function (Blueprint $table) {
            // Foto bukti verifikasi dari kurikulum
            $table->string('foto_verifikasi')->nullable()->after('keterangan');
            // Catatan kurikulum bahwa guru benar-benar mengajar
            $table->text('catatan_kurikulum')->nullable()->after('foto_verifikasi');
            // ID user kurikulum yang memverifikasi
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('catatan_kurikulum');
            // Waktu verifikasi
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('absensi_mengajar', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['foto_verifikasi', 'catatan_kurikulum', 'verified_by', 'verified_at']);
        });
    }
};
