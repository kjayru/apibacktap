<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public static function meses($fecha){
      
        $f = explode("-",$fecha);
        $mes  = $f[1];
        $year = $f[0];
        $dia = $f[2];

        if($mes==1){
           $mesnombre =  "January";
        }
        if($mes==2){
            $mesnombre = "February";
        }
        if($mes==3){
            $mesnombre = "March";
        }
        if($mes==4){
            $mesnombre = "April";
        }
        if($mes==5){
            $mesnombre = "May";
        }
        if($mes==6){
            $mesnombre = "June";
        }
        if($mes==7){
            $mesnombre = "July";
        }
        if($mes==8){
            $mesnombre = "August";
        }
        if($mes==9){
            $mesnombre = "September";
        }
        if($mes==10){
            $mesnombre = "Octuber";
        }
        if($mes==11){
            $mesnombre = "November";
        }
        if($mes==12){
            $mesnombre = "December";
        }
      
        $fechas= ['mes'=>$mes,"year"=>$year,"mesNombre"=>$mesnombre,'dia'=>$dia];
        
        return $fechas;
    }
}
