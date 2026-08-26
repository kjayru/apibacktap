<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_course_id', 'chapter_id', 'quiz_result'])]
class UserCourseChapter extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'user_course_id',
        'chapter_id',
        'quiz_result',
    ];
    use HasFactory;
    public function userCourse(){
        return $this->belongsTo(UserCourse::class);
    }

    public function userCourseChapterContents(){
        return $this->hasMany(UserCourseChapterContent::class);
    }

    public function usercoursechapterquizes(){
        return $this->hasMany(UserCourseChapterQuiz::class);
    }

    public function quizzes()
    {
        return $this->hasMany(UserCourseChapterQuiz::class, 'user_course_chapter_id');
    }

    public static function completeChapter($user_id,$course_id,$user_course_id){
        $courseChapter=0;
         $conteo_contenido = 0;
        $contenido_realizado = 0;
        $usercourse = UserCourse::find($user_course_id);
        $porcentaje = 0;

        if(UserCourseChapter::where('user_course_id',$usercourse->id)->count()>0){
            $courseChapter = UserCourseChapter::where('user_course_id',$usercourse->id)->count();

            //conteos_contenido_tomad
            $curso_capitulos = UserCourseChapter::where('user_course_id',$usercourse->id)->get();

            foreach($curso_capitulos as $cp){
                foreach($cp->userCourseChapterContents as $ucc){
                    $contenido_realizado +=1;
                }
            }

        }

        ///obtenemos contenidos por capitulo
        $chapters = Chapter::where('course_id',$course_id)->get();
        foreach($chapters as $chapter){
            foreach($chapter->chaptercontents as  $content){
                $conteo_contenido+=1;
            }
        }

        //dd(round($conteo_contenido/$contenido_realizado)*100,2);

       // $course= Course::find($course_id);
      //  $chapters = Chapter::where('course_id',$course->id)->count();
      if($contenido_realizado>0){
        $porcentaje = round($contenido_realizado*100/$conteo_contenido ,2);
      }
        return $porcentaje;
    }

    public static function cursandoContenido($slug,$active_slug,$user_course_id){
         $activo=false;
         if(Chaptercontent::where('slug',$slug)->count()>0){
            if($slug==$active_slug){
                $activo =  true;
            }
         }

         return $activo;

    }

    public static function capituloCursado($capitulo_id,$user_course_id){
        $activo = false;
        if(UserCourseChapter::where('chapter_id',$capitulo_id)->where('user_course_id',$user_course_id)->count()>0){
            $activo = true;
        }

        return $activo;
    }

    public static function userQuiz($user_course_id,$chapter_id){


              $activo = false;
              $contenidosCompletados=0;
        // if(UserCourseChapter::where('user_course_id',$user_course_id)->where('chapter_id',$chapter_id)->where('quiz_result',1)->count()>0){
        //     $activo = true;
        // }


        // if(UserCourseChapter::where('user_course_id',$user_course_id)->where('chapter_id',$chapter_id)->count()>0){
        // $ucc = UserCourseChapter::where('user_course_id',$user_course_id)->where('chapter_id',$chapter_id)->first();

        // $contenidosCompletados = UserCourseChapterContent::where('user_course_chapter_id',$ucc->id)->count();
        // }

        // $contenidosCapitulos = Chaptercontent::where('chapter_id',$chapter_id)->count();

        // if($contenidosCompletados == $contenidosCapitulos){
        //     $activo = true;
        // }
        if(UserCourseChapter::where('user_course_id',$user_course_id)->where('chapter_id',$chapter_id)->where('quiz_result',1)->count()>0){
            $activo = true;
        }


        return $activo;

    }
}
