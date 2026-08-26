<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use GeneratesSlug;

    protected $table = "categories";

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'name',
        'slug',
        'card',
        'banner',
        'orden',
        'parent_id',
    ];

    public function industries(){
        return $this->hasMany(Industry::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
