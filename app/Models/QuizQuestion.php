<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'quiz_id',
        'question',
    ];
    use HasFactory;
    public function quizQuestionOptions(){
        return $this->hasMany(QuizQuestionOption::class);
    }

    public function quiz(){
        return $this->belongsTo(Quiz::class);
    }
}
