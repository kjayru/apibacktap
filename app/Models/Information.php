<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    protected $table = "informations";

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'lastname',
        'firstname',
        'mi',
        'date',
        'address',
        'apartment',
        'city',
        'state',
        'zipcode',
        'phone',
        'email',
        'birthday',
        'socialnumber',
        'placebirth',
        'appliedpay',
        'whichshift',
        'whichday',
        'citizen',
        'authorized',
        'when',
        'explain1',
        'explain2',
        'worked',
        'convicted',
        'indictment',
    ];


    public function education(){
        return $this->hasOne(Education::class);
    }

    public function references(){
        return $this->hasMany(Reference::class);
    }

    public function employments(){
        return $this->hasMany(Employment::class);
    }

    public function military(){
        return $this->hasOne(Military::class);
    }

    public function disclaimer(){
        return $this->hasOne(Disclaimer::class);
    }
}
