<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\Estudio;
use App\Models\Horario;
use App\Models\Profesional;
use Illuminate\Database\Seeder;

class ProfesionalSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->profesionales() as $datos) {
            $especialidad = Especialidad::firstOrCreate(['nombre' => $datos['especialidad']]);
            $profesional = Profesional::updateOrCreate(
                ['nombre' => $datos['nombre']],
                [
                    'especialidad_id' => $especialidad->id,
                    'rango_edad_atencion' => $datos['rango_edad_atencion'],
                ],
            );

            $estudioIds = collect($datos['estudios'])
                ->map(fn (string $nombre): int => Estudio::firstOrCreate(['nombre' => $nombre])->id)
                ->all();

            $profesional->estudios()->sync($estudioIds);

            foreach ($datos['horarios'] as $horario) {
                Horario::updateOrCreate(
                    [
                        'profesional_id' => $profesional->id,
                        'dia_semana' => $horario['dia_semana'],
                        'hora_inicio' => $horario['hora_inicio'],
                    ],
                    ['hora_fin' => $horario['hora_fin']],
                );
            }
        }
    }

    /**
     * @return array<int, array{especialidad: string, nombre: string, rango_edad_atencion: ?string, estudios: array<int, string>, horarios: array<int, array{dia_semana: string, hora_inicio: string, hora_fin: string}>}>
     */
    private function profesionales(): array
    {
        return [
            [
                'especialidad' => 'Cardiología', 'nombre' => 'Dr. Cristian Chávez', 'rango_edad_atencion' => 'a partir de 12 años',
                'estudios' => ['Ecocardiograma Doppler color', 'Eco Doppler carótidas y vertebrales', 'Ecografía de vasos del cuello (arterial y venoso)'],
                'horarios' => [['dia_semana' => 'martes', 'hora_inicio' => '13:00', 'hora_fin' => '15:00'], ['dia_semana' => 'jueves', 'hora_inicio' => '16:00', 'hora_fin' => '17:15']],
            ],
            [
                'especialidad' => 'Ginecología / Ecografías', 'nombre' => 'Dra. Celeste Ramírez', 'rango_edad_atencion' => 'a partir de 11 años',
                'estudios' => ['Ecografía ginecológica abdominal', 'Ecografía transvaginal ginecológica', 'Monitoreo fetal', 'Perfil biofísico fetal', 'Ecografía obstétrica', 'Ecografía transvaginal obstétrica', 'PAP + Colposcopía'],
                'horarios' => [['dia_semana' => 'martes', 'hora_inicio' => '15:00', 'hora_fin' => '19:00'], ['dia_semana' => 'miercoles', 'hora_inicio' => '08:00', 'hora_fin' => '11:00'], ['dia_semana' => 'viernes', 'hora_inicio' => '08:00', 'hora_fin' => '11:30'], ['dia_semana' => 'viernes', 'hora_inicio' => '14:00', 'hora_fin' => '18:40']],
            ],
            [
                'especialidad' => 'Ecografía Ginecológica', 'nombre' => 'Dr. Ricardo Rojas', 'rango_edad_atencion' => 'empieza a tener relaciones menor de edad venir acompañada',
                'estudios' => ['Ecografía transvaginal ginecológica', 'Ecografía ginecológica abdominal', 'Ecografía obstétrica 1er trimestre (4 a 10 semanas)', 'Ecografía obstétrica (2do a 3er trimestre)', 'Ecografía transvaginal obstétrica', 'Ecografía de marcadores cromosómicos (11 a 13.6 semanas)', 'Ecografía morfológica fetal (2do trimestre, 20 a 24 semanas)', 'Eco Doppler materno fetal, umbilical, cerebral medio, uterino y ductus venoso', 'Ecografía cervicometría'],
                'horarios' => [['dia_semana' => 'jueves', 'hora_inicio' => '08:00', 'hora_fin' => '10:45'], ['dia_semana' => 'miercoles', 'hora_inicio' => '13:30', 'hora_fin' => '15:30']],
            ],
            [
                'especialidad' => 'Ecografía General', 'nombre' => 'Dra. Rita Miranda', 'rango_edad_atencion' => 'a partir de 15 años',
                'estudios' => ['Ecografía abdominal superior', 'Ecografía abdominal inferior', 'Ecografía abdominal completa (superior e inferior)', 'Ecografía prostática', 'Ecografía renal', 'Ecografía de partes blandas', 'Ecografía de tiroides', 'Ecografía testicular', 'Ecografía de mamas (masculino y femenino)'],
                'horarios' => [['dia_semana' => 'miercoles', 'hora_inicio' => '16:00', 'hora_fin' => '19:30']],
            ],
            [
                'especialidad' => 'Ecografía General', 'nombre' => 'Dr. Robert Alonso', 'rango_edad_atencion' => 'a partir de 2 años',
                'estudios' => ['Ecografía de tiroides / Doppler de tiroides', 'Ecografía testicular con doppler', 'Ecografía abdominal superior / inferior', 'Ecografía abdominal completa', 'Ecografía renal, vías urinarias', 'Ecografía testicular y cordón espermático', 'Ecografía vésico prostática', 'Ecografía de partes blandas'],
                'horarios' => [['dia_semana' => 'lunes', 'hora_inicio' => '08:00', 'hora_fin' => '11:00']],
            ],
            [
                'especialidad' => 'Ecografía General', 'nombre' => 'Dra. Paola Barreto', 'rango_edad_atencion' => 'a partir de 1 año',
                'estudios' => ['Ecografía abdominal inferior', 'Ecografía abdominal superior', 'Ecografía abdominal completa (superior e inferior)', 'Ecografía vésico prostática', 'Ecografía renal', 'Ecografía de partes blandas', 'Ecografía de tiroides', 'Ecografía testicular', 'Ecografía de mamas (masculino y femenino)'],
                'horarios' => [['dia_semana' => 'jueves', 'hora_inicio' => '12:00', 'hora_fin' => '15:30']],
            ],
            [
                'especialidad' => 'Ecografía General', 'nombre' => 'Dra. Clara González', 'rango_edad_atencion' => 'a partir de 12 años',
                'estudios' => ['Ecografía abdominal inferior', 'Ecografía abdominal superior', 'Ecografía abdominal completa (superior e inferior)', 'Ecografía de tiroides', 'Ecografía renal y vías urinarias', 'Ecografía prostática', 'Ecografía testicular', 'Angiopower por miembro', 'Ecografía de partes blandas y angiopower articular'],
                'horarios' => [['dia_semana' => 'lunes', 'hora_inicio' => '13:00', 'hora_fin' => '18:15'], ['dia_semana' => 'miercoles', 'hora_inicio' => '08:00', 'hora_fin' => '11:45']],
            ],
            [
                'especialidad' => 'Reumatología', 'nombre' => 'Dra. Carolina Franco', 'rango_edad_atencion' => null, 'estudios' => ['Procedimiento / Infiltración'],
                'horarios' => [['dia_semana' => 'jueves', 'hora_inicio' => '08:00', 'hora_fin' => '09:40'], ['dia_semana' => 'martes', 'hora_inicio' => '16:00', 'hora_fin' => '19:15']],
            ],
            [
                'especialidad' => 'Traumatología', 'nombre' => 'Dr. Fernando Román', 'rango_edad_atencion' => null, 'estudios' => ['Procedimiento / Infiltración'],
                'horarios' => [['dia_semana' => 'miercoles', 'hora_inicio' => '17:30', 'hora_fin' => '19:30'], ['dia_semana' => 'viernes', 'hora_inicio' => '17:30', 'hora_fin' => '19:30']],
            ],
            [
                'especialidad' => 'Otorrinolaringología', 'nombre' => 'Dra. Adriana Ferreira', 'rango_edad_atencion' => null, 'estudios' => ['Endoscopia nasal', 'Lavado de oído (bilateral)'],
                'horarios' => [['dia_semana' => 'lunes', 'hora_inicio' => '16:00', 'hora_fin' => '19:00'], ['dia_semana' => 'sabado', 'hora_inicio' => '09:00', 'hora_fin' => '11:30']],
            ],
            [
                'especialidad' => 'Asma y Alergia', 'nombre' => 'Dra. Mónica González', 'rango_edad_atencion' => null, 'estudios' => ['Pruebas cutáneas / Prick test / Test del parche / Provocación / FENO / Inmunoterapia'],
                'horarios' => [['dia_semana' => 'jueves', 'hora_inicio' => '14:00', 'hora_fin' => '17:00'], ['dia_semana' => 'martes', 'hora_inicio' => '16:30', 'hora_fin' => '19:30']],
            ],
            [
                'especialidad' => 'Neumología', 'nombre' => 'Dr. César Martínez', 'rango_edad_atencion' => null, 'estudios' => ['Espirometría con prueba broncodilatadora', 'Espirometría basal'],
                'horarios' => [['dia_semana' => 'jueves', 'hora_inicio' => '16:30', 'hora_fin' => '18:30'], ['dia_semana' => 'lunes', 'hora_inicio' => '14:00', 'hora_fin' => '17:00'], ['dia_semana' => 'martes', 'hora_inicio' => '10:45', 'hora_fin' => '12:30'], ['dia_semana' => 'viernes', 'hora_inicio' => '10:00', 'hora_fin' => '12:00']],
            ],
        ];
    }
}
