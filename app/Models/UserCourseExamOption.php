<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourseExamOption extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'user_course_exam_id',
        'quiz_question_id',
        'quiz_question_option_id',
        'respuesta',
    ];
    use HasFactory;

    public function userCourseExam(){
        return $this->belongsTo(UserCourseExam::class);
    }
}
