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
            $table->string('rpp_status')->default('kosong')->after('rpp_file');
        });

        // Set existing users with rpp_file to 'pending'
        \Illuminate\Support\Facades\DB::table('users')->whereNotNull('rpp_file')->update(['rpp_status' => 'pending']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rpp_status');
        });
    }
};
