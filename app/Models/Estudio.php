<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Estudio extends Model
{
    protected $fillable = [
        'nombre',
        'categoria',
    ];

    public function profesionales(): BelongsToMany
    {
        return $this->belongsToMany(Profesional::class, 'profesional_estudio')
            ->withTimestamps();
    }
}
