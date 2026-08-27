<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChapterQuiz extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'user_id',
        'chapter_id',
        'puntos',
        'intentos',
    ];
    use HasFactory;

    public function userChapterQuizOptions(){
        return $this->hasMany(UserChapterQuizOption::class);
    }

    public static function verificar($id,$chapter_id){

       
        $userquiz = UserCourseChapter::where('user_course_id',$id)->where('chapter_id',$chapter_id)->where('quiz_result','1')->first();
      
       

        $activo = false;
        if(isset($userquiz)){
            //if($userquiz->usercoursechapter->chapter_id == $chapter_id){
                $activo= true;
            //}
        }

        return $activo;
    }

    public static function observar($id,$chapter_id){

        $userquiz = UserCourseChapter::where('user_course_id',$id)->where('chapter_id',$chapter_id)->where('quiz_result','0')->first();
      
       

        $activo = false;
        if(isset($userquiz)){
           
                $activo= true;
            
        }

        return $activo;
    }
}
