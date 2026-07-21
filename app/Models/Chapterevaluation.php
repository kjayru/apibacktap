<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapterevaluation extends Model
{
    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }

    public function evaluationoptions(){
        return $this->hasMany(Evaluationoption::class);
    }
}
