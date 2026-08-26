<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluationoption extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'option',
        'answer',
        'chapterevaluation_id',
    ];
    public function chapterevaluation(){
        return $this->belongsTo(chapterevaluation::class);
    }
}
