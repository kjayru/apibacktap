<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapterevaluation extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'question',
        'chapter_id',
    ];
    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }

    public function evaluationoptions(){
        return $this->hasMany(Evaluationoption::class);
    }
}
