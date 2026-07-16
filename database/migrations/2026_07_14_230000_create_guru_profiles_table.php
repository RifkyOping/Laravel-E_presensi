<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('is_jadwal_set')->default(false);
            $table->string('rpp_file')->nullable();
            $table->string('rpp_status')->nullable(); // pending, disetujui, ditolak
            $table->text('rpp_pesan')->nullable();
            $table->boolean('is_piket_sholat')->default(false);
            $table->boolean('is_piket_mengajar')->default(false);
            $table->timestamps();
        });

        // Data migration: pindahkan data dari users ke guru_profiles
        $gurus = DB::table('users')->where('role', 'guru')->get();
        foreach ($gurus as $guru) {
            DB::table('guru_profiles')->insert([
                'user_id'           => $guru->id,
                'is_jadwal_set'     => $guru->is_jadwal_set ?? false,
                'rpp_file'          => $guru->rpp_file ?? null,
                'rpp_status'        => $guru->rpp_status ?? null,
                'rpp_pesan'         => $guru->rpp_pesan ?? null,
                'is_piket_sholat'   => $guru->is_piket_sholat ?? false,
                'is_piket_mengajar' => $guru->is_piket_mengajar ?? false,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_profiles');
    }
};
