<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use GeneratesSlug;


    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'title',
        'contenido',
        'course_id',
        'slug',
        'video',
        'reading',
        'audio',
        'quiz',
        'order',
    ];
    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function chaptercontents(){
        return $this->hasMany(Chaptercontent::class)->orderBy('order','asc');
    }

    public function chapterevaluations(){
        return $this->hasMany(Chapterevaluation::class);
    }

    /*public function attributes(){
        return $this->belongsToMany(Attribute::class);
    }*/

    public function attributechapters(){
        return $this->hasMany(Attributechapter::class);
    }

    public function chapterquiz(){
        return $this->hasOne(ChapterQuiz::class);
    }

    public function chapterquizzes(){
        return $this->hasMany(ChapterQuiz::class);
    }

    public static function capitulos($id){

        $capitulos = Chaptercontent::where('chapter_id',$id)->count();

        return $capitulos;
    }

    public static function indices($arreglo,$chapter_id){

        foreach($arreglo as $row){
            if($row['chapter_id']==$chapter_id){

               return $row['order'];
            }
        }
    }

    public static function proximo($arreglo,$chapter_id){


        $ordenActual =0;
        foreach($arreglo as $row){
            if($row['chapter_id']==$chapter_id){

              $ordenActual = $row['order'];
            }
        }

        if($ordenActual !=0){
            $indice = $ordenActual+1;


            foreach($arreglo as $row){
                if($row['order']==$indice){
                    return $row['chapter_id'];
                }
            }
        }

        return 0;
    }
}
