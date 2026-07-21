<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Military extends Model
{
    public function information(){
        return $this->belongsTo(Information::class);
    }
}
