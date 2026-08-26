<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = "educations";

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'information_id',
        'graduatehigh',
        'hightschool',
        'graduatecollage',
        'activecard',
        'firearm',
        'others',
        'highfrom',
        'hightto',
        'collaganame',
        'collagefrom',
        'collageto',
        'whatmayor',
        'completed',
        'officer',
        'holster',
    ];

    public function information(){
        return $this->belongsTo(Information::class);
    }
}
