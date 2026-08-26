<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use Illuminate\Database\Eloquent\Model;

class Chaptercontent extends Model
{
    use GeneratesSlug;


    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'titulo',
        'slug',
        'video',
        'poster',
        'contenido',
        'chapter_id',
        'audio',
        'order',
    ];
    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }
}
