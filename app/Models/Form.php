<?php

namespace App\Models;

use App\Models\Concerns\HasForm8850Statements;
use Illuminate\Database\Eloquent\Model;

/** Envíos del formulario 8850 de la web. */
class Form extends Model
{
    use HasForm8850Statements;


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
