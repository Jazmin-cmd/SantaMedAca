Nuevo mensaje desde el formulario de contacto del sitio web.

Motivo: {{ $datos['motivo'] }}
Nombre: {{ $datos['nombre'] }}
Correo: {{ $datos['email'] }}
@if (! empty($datos['telefono']))
Teléfono: {{ $datos['telefono'] }}
@endif

Mensaje:
{{ $datos['mensaje'] }}

—
Para responder, contestá este correo: la respuesta le llega directamente a {{ $datos['email'] }}.
