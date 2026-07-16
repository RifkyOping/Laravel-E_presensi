<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_jadwal_set',
                'rpp_file',
                'rpp_status',
                'rpp_pesan',
                'is_piket_sholat',
                'is_piket_mengajar',
                'skip_voice_verification',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_jadwal_set')->default(false)->after('device_id');
            $table->boolean('skip_voice_verification')->default(false)->after('is_jadwal_set');
            $table->boolean('is_piket_sholat')->default(false)->after('skip_voice_verification');
            $table->boolean('is_piket_mengajar')->default(false)->after('is_piket_sholat');
            $table->string('rpp_file')->nullable()->after('is_piket_mengajar');
            $table->string('rpp_status')->nullable()->after('rpp_file');
            $table->text('rpp_pesan')->nullable()->after('rpp_status');
        });
    }
};
