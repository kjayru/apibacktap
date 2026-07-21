<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    public function examquestions(){
        return $this->hasMany(ExamQuestion::class);
    }

    public function examcourse(){
        return $this->hasOne(ExamCourse::class);
    }

    public static function cursado($user_course_id,$exam_id){



        $activo = false;
        if(UserCourseExam::where('user_course_id',$user_course_id)->where('exam_id',$exam_id)->count()>0){
            $activo = true;
        }

        return $activo;
    }

}
