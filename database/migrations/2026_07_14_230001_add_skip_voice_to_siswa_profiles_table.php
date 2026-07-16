<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa_profiles', function (Blueprint $table) {
            $table->boolean('skip_voice_verification')->default(false)->after('agama');
        });

        // Data migration: pindahkan nilai skip_voice_verification dari users ke siswa_profiles
        $siswaProfiles = DB::table('siswa_profiles')->get();
        foreach ($siswaProfiles as $profile) {
            $user = DB::table('users')->where('id', $profile->user_id)->first();
            if ($user) {
                DB::table('siswa_profiles')
                    ->where('id', $profile->id)
                    ->update(['skip_voice_verification' => $user->skip_voice_verification ?? false]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('siswa_profiles', function (Blueprint $table) {
            $table->dropColumn('skip_voice_verification');
        });
    }
};
