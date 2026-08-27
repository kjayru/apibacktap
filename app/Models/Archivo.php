<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $table = "archivos";

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'file',
        'disclaimer_id',
    ];
}
