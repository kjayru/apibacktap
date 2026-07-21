<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterQuizOption extends Model
{
    use HasFactory;
    public function chapterquiz(){
        return $this->belongsTo(ChapterQuiz::class);
    }
    public function userSelections()
    {
        return $this->hasMany(UserCourseChapterQuiz::class, 'quiz_question_option_id');
    }

    public static function getResult($id){

        $answer = ChapterQuizOption::where('id',$id)->where('estado',1)->first();

        //dd($answer->id);
       if(isset($answer)){
        return true;
       }

       return false;

    }

    public static function isCorrect($id){
        //resultado usuario
        $resultUser = chapterQuizOption::where('id',$id)->first();

        if($resultUser->estado==1){
            return true;
        }
        return false;
        // $resultOption = ChapterQuizOption::where('id',$id)->where('estado',1)->first();
        // //se compara

        // if(isset($resultUser->id)){
        //    if(isset($resultOption->id)){
        //         if($resultUser->chapter_question_option_id==$resultOption->id){
        //             return $resultUser->chapter_question_option_id." verdad ".$resultOption->id;
        //         }else{
        //             return false;
        //         }
        //     }else{
        //         return false;
        //     }
        // }
        // return false;

    }

    public static function resultUser($id,$user_course_chapter_id){
        $resultUser = UserCourseChapterQuiz::where('quiz_question_option_id',$id)->where('user_course_chapter_id',$user_course_chapter_id)->first();
        if(isset($resultUser)){
            if($resultUser->result==1){
                return 1;
            }else{
                return 2;
            }
        }

        return 3;

    }
}
