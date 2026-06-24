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
        Schema::create('siswa_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nis', 20)->nullable()->unique();
            $table->string('nisn', 20)->nullable()->unique();
            $table->string('kelas', 50)->nullable();
            $table->string('jurusan', 100)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama', 50)->nullable();
            $table->timestamps();
        });

        // Pindahkan data dari tabel users (yang memiliki role='siswa') ke tabel siswa_profiles
        $siswas = DB::table('users')->where('role', 'siswa')->get();
        foreach ($siswas as $siswa) {
            DB::table('siswa_profiles')->insert([
                'user_id' => $siswa->id,
                'nis' => $siswa->nis,
                'nisn' => $siswa->nisn,
                'kelas' => $siswa->kelas,
                'jurusan' => $siswa->jurusan,
                'jenis_kelamin' => $siswa->jenis_kelamin,
                'tempat_lahir' => $siswa->tempat_lahir,
                'tanggal_lahir' => $siswa->tanggal_lahir,
                'agama' => $siswa->agama,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Hapus kolom dari tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nis', 'nisn', 'kelas', 'jurusan',
                'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan kolom ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('nis', 20)->nullable()->unique()->after('role');
            $table->string('nisn', 20)->nullable()->unique()->after('nis');
            $table->string('kelas', 50)->nullable()->after('nisn');
            $table->string('jurusan', 100)->nullable()->after('kelas');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('jurusan');
            $table->string('tempat_lahir', 100)->nullable()->after('jenis_kelamin');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('agama', 50)->nullable()->after('tanggal_lahir');
        });

        // Kembalikan data dari siswa_profiles ke users
        $profiles = DB::table('siswa_profiles')->get();
        foreach ($profiles as $profile) {
            DB::table('users')->where('id', $profile->user_id)->update([
                'nis' => $profile->nis,
                'nisn' => $profile->nisn,
                'kelas' => $profile->kelas,
                'jurusan' => $profile->jurusan,
                'jenis_kelamin' => $profile->jenis_kelamin,
                'tempat_lahir' => $profile->tempat_lahir,
                'tanggal_lahir' => $profile->tanggal_lahir,
                'agama' => $profile->agama,
            ]);
        }

        Schema::dropIfExists('siswa_profiles');
    }
};
