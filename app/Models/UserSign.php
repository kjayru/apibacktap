<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'legalname', 'email', 'firma', 'fullname', 'initial', 'code'])]
class UserSign extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'user_id',
        'legalname',
        'email',
        'firma',
        'fullname',
        'initial',
        'code',
    ];
    use HasFactory;
}
