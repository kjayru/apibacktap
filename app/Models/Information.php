<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    protected $table = "informations";


    public function education(){
        return $this->hasOne(Education::class);
    }

    public function references(){
        return $this->hasMany(Reference::class);
    }

    public function employments(){
        return $this->hasMany(Employment::class);
    }

    public function military(){
        return $this->hasOne(Military::class);
    }

    public function disclaimer(){
        return $this->hasOne(Disclaimer::class);
    }
}
