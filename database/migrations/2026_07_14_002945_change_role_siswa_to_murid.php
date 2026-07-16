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
        // 1. Tambahkan 'murid' ke enum dan ubah default
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','guru','siswa','murid','pengawas','kurikulum') NOT NULL DEFAULT 'murid'");
        
        // 2. Update data 'siswa' menjadi 'murid'
        \Illuminate\Support\Facades\DB::table('users')->where('role', 'siswa')->update(['role' => 'murid']);
        
        // 3. Hapus 'siswa' dari enum
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','guru','murid','pengawas','kurikulum') NOT NULL DEFAULT 'murid'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Tambahkan kembali 'siswa' ke enum
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','guru','siswa','murid','pengawas','kurikulum') NOT NULL DEFAULT 'siswa'");
        
        // 2. Kembalikan data 'murid' menjadi 'siswa'
        \Illuminate\Support\Facades\DB::table('users')->where('role', 'murid')->update(['role' => 'siswa']);
        
        // 3. Hapus 'murid' dari enum
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','guru','siswa','pengawas','kurikulum') NOT NULL DEFAULT 'siswa'");
    }
};
