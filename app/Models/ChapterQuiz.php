<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterQuiz extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'chapter_id',
        'quiz_id',
        'question',
    ];
    use HasFactory;
    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }
    public function chapterquizoptions(){
        return $this->hasMany(ChapterQuizOption::class);
    }

    public function options()
    {
        return $this->hasMany(ChapterQuizOption::class, 'chapter_quiz_id');
    }

    public static function getAnswer($id){

       $result = null;

       if(ChapterQuizOption::where('chapter_quiz_id',$id)->count()>0){
         $answer = ChapterQuizOption::where('chapter_quiz_id',$id)->where('estado',1)->first();
         $result = $answer->option;
       }
        return $result;

    }


    public static function pasoQuiz($id,$user_course_id,$curso_id,$chapter_id){

        $capitulo = Chapter::where('course_id',$curso_id)->where('id','<',$chapter_id)->orderBy('id')->first();
        // return $capitulo;
        if(isset($capitulo)){
            $aprobo = UserCourseChapter::where('user_course_id',$user_course_id)->where('chapter_id',$capitulo->id)->first();
           if(isset($aprobo)){
                if($aprobo->quiz_result==1){
                        return true;
                }
            }
               return false;
          }
        return false;
    }


    public static function cursandoQuiz($slug,$chapter_id,$quiz){

        $activo = false;
        if(isset($slug)){
            $capmenu = Chapter::where('slug',$slug)->first();

            $capactivo = Chapter::where('id',$chapter_id)->first();

            if($capmenu->slug == $capactivo->slug && $quiz==true){
                $activo = true;
            }
        }

        return $activo;

    }







    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }
}
