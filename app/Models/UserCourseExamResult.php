<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_course_exam_id', 'attempt_number', 'exam_question_id', 'exam_question_option_id', 'result'])]
class UserCourseExamResult extends Model
{
    use HasFactory;

    public function userCourseExam()
    {
        return $this->belongsTo(UserCourseExam::class);
    }

    public function examQuestion()
    {
        return $this->belongsTo(ExamQuestion::class);
    }

    public function examQuestionOption()
    {
        return $this->belongsTo(ExamQuestionOption::class);
    }
}
