<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = "educations";

    public function information(){
        return $this->belongsTo(Information::class);
    }
}
