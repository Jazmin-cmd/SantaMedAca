<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfesionalRequest;
use App\Http\Resources\ProfesionalResource;
use App\Models\Profesional;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class ProfesionalController extends Controller
{
    private const RELACIONES = ['especialidad', 'estudios', 'horarios'];

    /**
     * Lista paginada. Filtros opcionales: ?especialidad_id=, ?dia=, ?buscar=, ?por_pagina=
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $profesionales = Profesional::query()
            ->with(self::RELACIONES)
            ->when($request->integer('especialidad_id'), fn ($q, $id) => $q->where('especialidad_id', $id))
            ->when($request->string('dia')->toString(), fn ($q, $dia) => $q->whereHas('horarios', fn ($h) => $h->where('dia_semana', $dia)))
            ->when($request->string('buscar')->trim()->toString(), fn ($q, $texto) => $q->where('nombre', 'like', "%{$texto}%"))
            ->orderBy('nombre')
            ->paginate(min($request->integer('por_pagina', 15), 100));

        return ProfesionalResource::collection($profesionales);
    }

    public function store(ProfesionalRequest $request): JsonResponse
    {
        $profesional = DB::transaction(function () use ($request) {
            $profesional = Profesional::create($request->safe()->only(['nombre', 'especialidad_id', 'rango_edad_atencion']));
            $this->sincronizarRelaciones($profesional, $request);

            return $profesional;
        });

        return (new ProfesionalResource($profesional->load(self::RELACIONES)))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Profesional $profesional): ProfesionalResource
    {
        return new ProfesionalResource($profesional->load(self::RELACIONES));
    }

    public function update(ProfesionalRequest $request, Profesional $profesional): ProfesionalResource
    {
        DB::transaction(function () use ($request, $profesional) {
            $profesional->update($request->safe()->only(['nombre', 'especialidad_id', 'rango_edad_atencion']));
            $this->sincronizarRelaciones($profesional, $request);
        });

        return new ProfesionalResource($profesional->fresh(self::RELACIONES));
    }

    public function destroy(Profesional $profesional): JsonResponse
    {
        // Las FK no tienen cascade, así que se limpian las relaciones antes de borrar.
        DB::transaction(function () use ($profesional) {
            $profesional->horarios()->delete();
            $profesional->estudios()->detach();
            $profesional->delete();
        });

        return response()->json(null, 204);
    }

    /**
     * Si el request trae "estudios" u "horarios", reemplaza los existentes por los enviados.
     * Si no los trae, no se tocan (útil para PATCH parcial).
     */
    private function sincronizarRelaciones(Profesional $profesional, ProfesionalRequest $request): void
    {
        if ($request->has('estudios')) {
            $profesional->estudios()->sync($request->validated('estudios', []));
        }

        if ($request->has('horarios')) {
            $profesional->horarios()->delete();
            $profesional->horarios()->createMany($request->validated('horarios', []));
        }
    }
}