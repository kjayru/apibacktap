<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employment extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'information_id',
        'company',
        'phoneemp',
        'addressempl',
        'supervisor',
        'jobtitle',
        'starting',
        'ending',
        'from',
        'to',
        'reason',
        'references',
    ];
    public function information(){
        return $this->belongsTo(Information::class);
    }
}
