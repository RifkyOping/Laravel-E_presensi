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
            $table->text('webauthn_public_key')->nullable()->after('device_id');
            $table->unsignedBigInteger('webauthn_sign_count')->default(0)->after('webauthn_public_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['webauthn_public_key', 'webauthn_sign_count']);
        });
    }
};
