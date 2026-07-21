<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;
    public function quizQuestionOptions(){
        return $this->hasMany(QuizQuestionOption::class);
    }

    public function quiz(){
        return $this->belongsTo(Quiz::class);
    }
}
