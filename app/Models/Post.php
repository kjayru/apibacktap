<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use GeneratesSlug;


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
