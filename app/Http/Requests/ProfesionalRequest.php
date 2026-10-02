<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validación y reglas de negocio para alta y edición de profesionales.
 *
 * Reglas de negocio:
 *  - El nombre del profesional es único.
 *  - La especialidad debe existir.
 *  - Los estudios asignados deben existir y no repetirse.
 *  - Cada horario: día válido (lunes a sábado), formato HH:MM y hora_fin posterior a hora_inicio.
 *  - Los horarios de un mismo profesional no pueden superponerse en el mismo día.
 */
class ProfesionalRequest extends FormRequest
{
    public const DIAS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profesional = $this->route('profesional');
        $parcial = $this->isMethod('PATCH');
        $requerido = $parcial ? 'sometimes' : 'required';

        return [
            'nombre' => [
                $requerido, 'string', 'min:3', 'max:255',
                Rule::unique('profesionales', 'nombre')->ignore($profesional?->id),
            ],
            'especialidad_id' => [$requerido, 'integer', 'exists:especialidades,id'],
            'rango_edad_atencion' => ['nullable', 'string', 'max:100'],

            'estudios' => ['sometimes', 'array'],
            'estudios.*' => ['integer', 'distinct', 'exists:estudios,id'],

            'horarios' => ['sometimes', 'array'],
            'horarios.*.dia_semana' => ['required', Rule::in(self::DIAS)],
            'horarios.*.hora_inicio' => ['required', 'date_format:H:i'],
            'horarios.*.hora_fin' => ['required', 'date_format:H:i', 'after:horarios.*.hora_inicio'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $porDia = collect($this->input('horarios', []))
                    ->map(fn (array $h, int $i) => $h + ['indice' => $i])
                    ->groupBy('dia_semana');

                foreach ($porDia as $dia => $horarios) {
                    $ordenados = $horarios->sortBy('hora_inicio')->values();

                    for ($i = 1; $i < $ordenados->count(); $i++) {
                        $anterior = $ordenados[$i - 1];
                        $actual = $ordenados[$i];

                        if ($actual['hora_inicio'] < $anterior['hora_fin']) {
                            $validator->errors()->add(
                                "horarios.{$actual['indice']}.hora_inicio",
                                "El horario {$actual['hora_inicio']}-{$actual['hora_fin']} del {$dia} se superpone con {$anterior['hora_inicio']}-{$anterior['hora_fin']}."
                            );
                        }
                    }
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un profesional con ese nombre.',
            'especialidad_id.exists' => 'La especialidad seleccionada no existe.',
            'estudios.*.exists' => 'Uno de los estudios seleccionados no existe.',
            'estudios.*.distinct' => 'Hay estudios repetidos.',
            'horarios.*.dia_semana.in' => 'El día debe ser de lunes a sábado.',
            'horarios.*.hora_inicio.date_format' => 'La hora de inicio debe tener formato HH:MM.',
            'horarios.*.hora_fin.date_format' => 'La hora de fin debe tener formato HH:MM.',
            'horarios.*.hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }
}