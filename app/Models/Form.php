<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'yourname',
        'socialnumber',
        'address',
        'country',
        'citystate',
        'telephone',
        'birthday',
        'condicional',
    ];
    //
}
