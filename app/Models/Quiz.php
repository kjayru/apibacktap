<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = "quizes";

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'title',
        'duration',
    ];

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function quizQuestions(){
        return $this->hasMany(QuizQuestion::class);
    }

    public static function preguntas($id){
         $preguntas = ChapterQuiz::where('chapter_id',$id)->count();

         return $preguntas;
    }
}
