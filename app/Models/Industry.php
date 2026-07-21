<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    public function Category()
    {
        return $this->belongsTo(Category::class);
    }
}
