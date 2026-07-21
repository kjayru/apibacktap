<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chaptercontent extends Model
{
    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }
}
