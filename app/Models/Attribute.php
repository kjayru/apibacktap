<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    public function attributercourses(){
        return $this->hasMany(Attributecourse::class);
    }

    public function attributechapters(){
        return $this->hasMany(Attributechapter::class);
    }
}
