<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChapterQuizOption extends Model
{
    use HasFactory;

    public function userChapterQuiz(){
        return $this->belongsTo(userChapterQuiz::class);
    }
    public static function aciertos($user_id,$chapter_id){

        $preguntas = ChapterQuiz::where('chapter_id',$chapter_id)->get();
        $totales=0;
       
        foreach($preguntas as $preg){
            $resultado = ChapterQuizOption::where('chapter_quiz_id',$preg->id)->where('estado',1)->first();
            $respuesta[] = $resultado->id;

            $resultadoUsuario = UserChapterQuizOption::where('user_id',$user_id)->where('chapter_question_option_id',$resultado->id)->first();
            if(isset($resultadoUsuario->id)){
                $totales+=1;
            }
        }

        
        return $totales." of ".count($respuesta);

    }

    public static function porcentaje($user_id,$chapter_id){

        $preguntas = ChapterQuiz::where('chapter_id',$chapter_id)->get();
        $totales=0;
       
        foreach($preguntas as $preg){
            $resultado = ChapterQuizOption::where('chapter_quiz_id',$preg->id)->where('estado',1)->first();
            $respuesta[] = $resultado->id;

            $resultadoUsuario = UserChapterQuizOption::where('user_id',$user_id)->where('chapter_question_option_id',$resultado->id)->first();
            if(isset($resultadoUsuario->id)){
                $totales+=1;
            }
        }

        $preguntas = count($respuesta);
        $porcentaje = round($totales*100/$preguntas ,2);
        return $porcentaje;

    }
}
