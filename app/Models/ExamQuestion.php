<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    use HasFactory;

    public function examquestionoptions(){
        return $this->hasMany(ExamQuestionOption::class);
    }

    public function exam(){
        return $this->belongsTo(Exam::class);
    }

    public static function getResult($id){
       
        $resultado = ExamQuestionOption::where('exam_question_id',$id)->where('resultado',1)->first();
      if(isset($resultado)){
        return $resultado->opcion;
      }
      return false;
    }
}

