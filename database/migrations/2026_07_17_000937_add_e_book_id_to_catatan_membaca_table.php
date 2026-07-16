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
        // Pengecekan: Hanya tambahkan jika kolom 'e_book_id' belum ada
        if (!Schema::hasColumn('catatan_membaca', 'e_book_id')) {
            Schema::table('catatan_membaca', function (Blueprint $table) {
                $table->foreignId('e_book_id')->nullable()->constrained('e_books')->onDelete('cascade');
            });
        }

        // Pengecekan: Hanya ubah 'buku_manual_id' jika kolom tersebut ada
        if (Schema::hasColumn('catatan_membaca', 'buku_manual_id')) {
            Schema::table('catatan_membaca', function (Blueprint $table) {
                $table->foreignId('buku_manual_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catatan_membaca', function (Blueprint $table) {
            // Hapus foreign key dan kolom jika rollback
            if (Schema::hasColumn('catatan_membaca', 'e_book_id')) {
                $table->dropForeign(['e_book_id']);
                $table->dropColumn('e_book_id');
            }
        });
    }
};
