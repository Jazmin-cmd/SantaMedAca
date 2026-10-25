<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactoRequest;
use App\Mail\MensajeContacto;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactoController extends Controller
{
    /**
     * Envía por correo el mensaje del formulario de contacto a la casilla
     * configurada en CONTACTO_DESTINO.
     */
    public function enviar(ContactoRequest $request): JsonResponse
    {
        $respuesta = response()->json(['message' => 'Recibimos tu mensaje. Te respondemos a la brevedad.']);

        // Honeypot completado: es un bot. Se responde igual que un envío
        // exitoso para no darle pistas, pero no se manda nada.
        if ($request->filled('sitio_web')) {
            return $respuesta;
        }

        Mail::to(config('services.contacto.destino'))
            ->send(new MensajeContacto($request->safe()->only(['nombre', 'email', 'telefono', 'motivo', 'mensaje'])));

        return $respuesta;
    }
}
