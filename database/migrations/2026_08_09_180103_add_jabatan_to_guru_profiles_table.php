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
        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->boolean('is_kepsek')->default(false)->after('is_guru_bahasa');
            $table->boolean('is_kurikulum')->default(false)->after('is_kepsek');
        });

        // Data migration: find users with role 'kurikulum', change role to 'guru' and set is_kurikulum = true
        $kurikulumUsers = DB::table('users')->where('role', 'kurikulum')->get();
        foreach ($kurikulumUsers as $user) {
            DB::table('users')->where('id', $user->id)->update(['role' => 'guru']);
            
            // Check if guru_profile exists, if not create one
            $profileExists = DB::table('guru_profiles')->where('user_id', $user->id)->exists();
            if ($profileExists) {
                DB::table('guru_profiles')->where('user_id', $user->id)->update(['is_kurikulum' => true]);
            } else {
                DB::table('guru_profiles')->insert([
                    'user_id' => $user->id,
                    'is_kurikulum' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->dropColumn(['is_kepsek', 'is_kurikulum']);
        });
        
        // Note: Downgrade will not revert the role back to 'kurikulum' as we cannot reliably determine 
        // who was originally a kurikulum vs a guru who later got assigned kurikulum.
    }
};
