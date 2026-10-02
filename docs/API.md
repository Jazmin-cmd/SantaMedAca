# API SantaMed

Base: `http://<host>:8080/api` · Respuestas en JSON · Errores de validación: `422` con `{ message, errors: { campo: [..] } }`

## Profesionales (CRUD principal)

- `GET /profesionales` — lista paginada. Filtros opcionales: `especialidad_id`, `dia` (lunes…sabado), `buscar` (por nombre), `por_pagina` (máx. 100).
- `GET /profesionales/{id}` — detalle con especialidad, estudios y horarios.
- `POST /profesionales` — alta (devuelve `201`).
- `PUT /profesionales/{id}` — edición completa.
- `PATCH /profesionales/{id}` — edición parcial: solo cambia los campos enviados.
- `DELETE /profesionales/{id}` — baja (devuelve `204`); borra también sus horarios y estudios asignados.

Cuerpo de alta/edición:

```json
{
  "nombre": "Dra. Ana Benítez",
  "especialidad_id": 1,
  "rango_edad_atencion": "a partir de 12 años",
  "estudios": [1, 3],
  "horarios": [
    { "dia_semana": "lunes", "hora_inicio": "08:00", "hora_fin": "12:00" }
  ]
}
```

Si se envían `estudios` u `horarios`, reemplazan a los anteriores. Si no se envían, se conservan.

### Reglas de negocio

- El nombre del profesional es obligatorio y único.
- La especialidad y los estudios deben existir; no se repiten estudios.
- Días válidos: lunes a sábado. Horas en formato `HH:MM`.
- La hora de fin debe ser posterior a la de inicio.
- Un profesional no puede tener horarios superpuestos el mismo día.

## Catálogos (para los selects del frontend)

- `GET /especialidades`
- `GET /estudios`

## Sesiones y caché

Sesiones y caché centralizadas en Redis (`SESSION_DRIVER=redis`, `CACHE_STORE=redis`, `REDIS_HOST=redis`). La imagen instala la extensión `phpredis`.

## Tests

```bash
php artisan test
```