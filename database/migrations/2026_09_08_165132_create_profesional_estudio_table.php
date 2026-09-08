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
        Schema::create('profesional_estudio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profesional_id')->constrained('profesionales');
            $table->foreignId('estudio_id')->constrained('estudios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesional_estudio');
    }
};
