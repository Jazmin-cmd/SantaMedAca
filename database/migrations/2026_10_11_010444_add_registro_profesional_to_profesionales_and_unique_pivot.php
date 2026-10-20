<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $vistos = [];
        $duplicados = [];
        $asignaciones = DB::table('profesional_estudio')
            ->orderBy('id')
            ->get(['id', 'profesional_id', 'estudio_id']);

        foreach ($asignaciones as $asignacion) {
            $clave = "{$asignacion->profesional_id}:{$asignacion->estudio_id}";

            if (isset($vistos[$clave])) {
                $duplicados[] = $asignacion->id;
            }

            $vistos[$clave] = true;
        }

        if ($duplicados !== []) {
            DB::table('profesional_estudio')->whereIn('id', $duplicados)->delete();
        }

        Schema::table('profesionales', function (Blueprint $table): void {
            $table->string('registro_profesional', 100)->nullable()->unique();
        });

        Schema::table('profesional_estudio', function (Blueprint $table): void {
            $table->unique(['profesional_id', 'estudio_id']);
        });
    }

    public function down(): void
    {
        Schema::table('profesional_estudio', function (Blueprint $table): void {
            $table->index('profesional_id');
            $table->dropUnique(['profesional_id', 'estudio_id']);
        });

        Schema::table('profesionales', function (Blueprint $table): void {
            $table->dropUnique(['registro_profesional']);
            $table->dropColumn('registro_profesional');
        });
    }
};
