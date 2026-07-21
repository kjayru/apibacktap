<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluationoption extends Model
{
    public function chapterevaluation(){
        return $this->belongsTo(chapterevaluation::class);
    }
}
