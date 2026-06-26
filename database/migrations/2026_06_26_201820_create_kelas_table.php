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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('tingkat');
            $table->string('jurusan');
            $table->string('rombel');
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            // Mencegah duplikasi kombinasi tingkat + jurusan + rombel
            $table->unique(['tingkat', 'jurusan', 'rombel'], 'kelas_unik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
