<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    public static function generarCupon(){

        //Generación de arreglos que se usaran para genera la variable unica de la url corta
        $vocales = array("a","e","i","o","u");
        $vocalesM = array("A","E","I","O","U");
        $consonantes = array("b","c","d","f","g","h","j","k","l","m","n","p","q","r","s","t","v","x","y","z");
        $consonantesM = array("B","C","D","F","G","H","J","K","L","M","N","P","Q","R","S","T","V","X","Y","Z");
        $inicio = 0;
        $final = 99;
        $letraAleatoria='';
        //generan bucles aleatorios entre los arreglos para generar la variable unica
        for($i=0; $i<1; $i++){
            $valor1= array_rand($vocales, 2);
            $vocal = $vocales[$valor1[0]];
            $letraAleatoria .= $vocal;
            for($l=0;$l<1;$l++){
                $valor4 = array_rand($consonantesM, 2);
                $consonante2 = $consonantesM[$valor4[0]];
                $letraAleatoria .= $consonante2;
                $letraAleatoria .= rand($inicio, $final);
            }
        }
        for($k=0;$k<1;$k++){
            $valor3 = array_rand($vocalesM, 2);
            $vocal2 = $vocalesM[$valor3[0]];
            $letraAleatoria .= $vocal2;
            $letraAleatoria .= rand($inicio, $final);
            // for($j=0;$j<1;$j++){
            //     $valor2 = array_rand($consonantes, 2);
            //     $consonante = $consonantes[$valor2[0]];
            //     $letraAleatoria .= $consonante;
            //     $letraAleatoria .= rand($inicio, $final);
            // }
        }

        return $letraAleatoria;
    }
}
