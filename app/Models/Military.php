<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Military extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'information_id',
        'branch',
        'from',
        'to',
        'rank',
        'type',
        'explain',
    ];
    public function information(){
        return $this->belongsTo(Information::class);
    }
}
