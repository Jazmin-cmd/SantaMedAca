<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use App\Models\Estudio;
use Illuminate\Http\JsonResponse;

class CatalogoController extends Controller
{
    public function especialidades(): JsonResponse
    {
        return response()->json([
            'data' => Especialidad::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function estudios(): JsonResponse
    {
        return response()->json([
            'data' => Estudio::orderBy('categoria')->orderBy('nombre')->get(['id', 'nombre', 'categoria']),
        ]);
    }
}