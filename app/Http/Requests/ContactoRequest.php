<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validación del formulario de contacto del sitio web.
 *
 * `sitio_web` es un campo trampa (honeypot): está oculto en el formulario, así
 * que solo lo completan los bots. El controlador descarta esos envíos.
 */
class ContactoRequest extends FormRequest
{
    public const MOTIVOS = ['Consulta general', 'Turnos', 'Estudios y resultados', 'Convenio Santa Clara', 'Otro'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'motivo' => ['required', Rule::in(self::MOTIVOS)],
            'mensaje' => ['required', 'string', 'min:10', 'max:3000'],
            'sitio_web' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'Ingresá tu nombre y apellido.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'email.required' => 'Ingresá tu correo electrónico.',
            'email.email' => 'El correo electrónico no es válido.',
            'motivo.required' => 'Elegí un motivo.',
            'motivo.in' => 'Elegí un motivo de la lista.',
            'mensaje.required' => 'Escribí tu mensaje.',
            'mensaje.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'mensaje.max' => 'El mensaje no puede superar los 3000 caracteres.',
        ];
    }
}
