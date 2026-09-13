<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Kosongkan semua device_id lama (berbasis UUID cookie) agar pengguna
     * bisa mendaftar ulang dengan fingerprint perangkat baru (FingerprintJS).
     */
    public function up(): void
    {
        DB::table('users')->update(['device_id' => null]);
    }

    /**
     * Reverse the migrations.
     * Catatan: data lama tidak dapat dipulihkan.
     */
    public function down(): void
    {
        // Tidak dapat dikembalikan
    }
};
