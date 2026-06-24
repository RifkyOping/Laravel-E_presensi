<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: ubah enum agar mencakup 'kurikulum'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','guru','siswa','pengawas','kurikulum') NOT NULL DEFAULT 'siswa'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','guru','siswa','pengawas') NOT NULL DEFAULT 'siswa'");
    }
};
