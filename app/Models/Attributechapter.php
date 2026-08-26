<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attributechapter extends Model
{
    use HasFactory;
    protected $table = "attributechapters";

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'chapter_id',
        'attribute_id',
        'valor',
    ];

    public function attribute(){
        return $this->belongsTo(Attribute::class);
    }

    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }
}
