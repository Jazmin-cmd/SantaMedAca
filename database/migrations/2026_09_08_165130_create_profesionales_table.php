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
        Schema::create('profesionales', function (Blueprint $table) {
            $table->id();
            $table->string('Cristian Chavez, Mauricio Lopez, Oscar Acosta'); 
            $table->foreignId('especialidad_id')->constrained('especialidades');
            $table->string('rango_edad_atencion')->nullable(); // "a partir de 12 años"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesionales');
    }
};
