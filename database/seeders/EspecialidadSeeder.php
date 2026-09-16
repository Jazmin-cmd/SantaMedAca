<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Seeder;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        Especialidad::upsert([
            ['nombre' => 'Cardiología'], ['nombre' => 'Ginecología / Ecografías'],
            ['nombre' => 'Ecografía Ginecológica'], ['nombre' => 'Ecografía General'],
            ['nombre' => 'Reumatología'], ['nombre' => 'Traumatología'],
            ['nombre' => 'Otorrinolaringología'], ['nombre' => 'Asma y Alergia'],
            ['nombre' => 'Neumología'],
        ], ['nombre'], []);
    }
}
