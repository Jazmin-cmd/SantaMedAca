<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profesional extends Model
{
    protected $fillable = [
        'nombre',
        'especialidad_id',
        'rango_edad_atencion',
    ];

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function estudios(): BelongsToMany
    {
        return $this->belongsToMany(Estudio::class, 'profesional_estudio')
            ->withTimestamps();
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }
}
