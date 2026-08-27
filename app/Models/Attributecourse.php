<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attributecourse extends Model
{
    use HasFactory;
    protected $table = "attributecourses";

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'course_id',
        'attribute_id',
        'valor',
    ];

    public function attribute(){
        return $this->belongsTo(Attribute::class);
    }

    public function course(){
        return $this->belongsTo(Course::class);
    }
}
