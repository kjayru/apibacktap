<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestionOption extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'option',
        'resultado',
        'identificador',
        'quiz_question_id',
    ];
    use HasFactory;
    public function quizQuestion(){
        return $this->belongsTo(QuizQuestion::class);
    }
}
