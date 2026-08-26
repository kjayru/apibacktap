<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'titulo',
        'slug',
        'card',
        'banner',
        'contenido',
        'resumen',
    ];
    //
}
