<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfesionalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'rango_edad_atencion' => $this->rango_edad_atencion,
            'especialidad' => $this->whenLoaded('especialidad', fn () => [
                'id' => $this->especialidad->id,
                'nombre' => $this->especialidad->nombre,
            ]),
            'estudios' => $this->whenLoaded('estudios', fn () => $this->estudios->map(fn ($e) => [
                'id' => $e->id,
                'nombre' => $e->nombre,
                'categoria' => $e->categoria,
            ])),
            'horarios' => $this->whenLoaded('horarios', fn () => $this->horarios->map(fn ($h) => [
                'id' => $h->id,
                'dia_semana' => $h->dia_semana,
                'hora_inicio' => substr((string) $h->getRawOriginal('hora_inicio'), 0, 5),
                'hora_fin' => substr((string) $h->getRawOriginal('hora_fin'), 0, 5),
            ])),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}