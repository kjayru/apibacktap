<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'titulo',
        'slug',
        'banner',
        'card',
        'contenido',
        'orden',
        'category_id',
    ];
    public function Category()
    {
        return $this->belongsTo(Category::class);
    }
}
