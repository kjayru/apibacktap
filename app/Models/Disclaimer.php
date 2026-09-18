<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disclaimer extends Model
{
    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'information_id',
        'signature',
        'fileid',
        'datedisclamer',
    ];

    public function information()
    {
        return $this->belongsTo(Information::class);
    }

    /** Los adjuntos van en la tabla archivos; la columna fileid no se ha usado nunca. */
    public function archivos()
    {
        return $this->hasMany(Archivo::class);
    }
}
