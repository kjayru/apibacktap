<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['chapter_quiz_id', 'user_course_chapter_id', 'quiz_question_option_id', 'result', 'timequiz'])]
class UserCourseChapterQuiz extends Model
{
    use HasFactory;

    public function usercoursechapter(){
        return $this->belongsTo(UserCourseChapter::class,'user_course_chapter_id','id');
    }
}
