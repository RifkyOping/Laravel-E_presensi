<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('latitude',  10, 7)->default(-3.5432);  // default SMKN 1 Majene
            $table->decimal('longitude', 10, 7)->default(118.9759);
            $table->integer('radius_meter')->default(200); // radius dalam meter
            $table->string('nama_sekolah')->default('SMKN 1 Majene');
            $table->timestamps();
        });

        // Insert default record
        DB::table('school_settings')->insert([
            'latitude'     => -3.5432,
            'longitude'    => 118.9759,
            'radius_meter' => 200,
            'nama_sekolah' => 'SMKN 1 Majene',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
