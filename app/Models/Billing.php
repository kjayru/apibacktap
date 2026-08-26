<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'name',
        'lastname',
        'email',
        'phone',
        'other_phone',
        'type_address',
        'address1',
        'address2',
        'colony',
        'zipcode',
        'status',
    ];
    //
}
