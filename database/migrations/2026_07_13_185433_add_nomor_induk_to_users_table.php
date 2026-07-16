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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nomor_induk')->nullable()->unique()->after('email');
            $table->string('email')->nullable()->change();
        });

        // Populate existing users with dummy nomor_induk
        $users = \App\Models\User::all();
        foreach ($users as $index => $user) {
            if ($user->role === 'admin') {
                $user->nomor_induk = 'admin';
            } elseif ($user->role === 'guru') {
                $user->nomor_induk = '19800101' . str_pad($index, 4, '0', STR_PAD_LEFT);
            } else {
                $user->nomor_induk = '00' . str_pad($index, 8, '0', STR_PAD_LEFT);
            }
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nomor_induk');
            $table->string('email')->nullable(false)->change();
        });
    }
};
