<?php

use App\Http\Controllers\Api\CatalogoController;
use App\Http\Controllers\Api\ContactoController;
use App\Http\Controllers\Api\ProfesionalController;
use Illuminate\Support\Facades\Route;

// CRUD principal: profesionales (con su especialidad, estudios y horarios)
Route::apiResource('profesionales', ProfesionalController::class)
    ->parameters(['profesionales' => 'profesional']);

// Catálogos de solo lectura para los selects del frontend
Route::get('especialidades', [CatalogoController::class, 'especialidades']);
Route::get('estudios', [CatalogoController::class, 'estudios']);

// Formulario de contacto del sitio: máximo 5 envíos por minuto por IP
Route::post('contacto', [ContactoController::class, 'enviar'])->middleware('throttle:5,1');