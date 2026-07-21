<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public function chapters(){
        return $this->hasMany(Chapter::class);
    }


    public function attributes(){
        return $this->belongsToMany(Attribute::class);
    }

    public function attributecourse(){
        return $this->belongsTo(Attributecourse::class);
    }

    public function usercourses(){
        return $this->hasMany(UserCourse::class);
    }

    public function quiz(){
        return $this->hasOne(Quiz::class);
    }

    public static function info($id){
        $course=Course::find($id);

        $datos = [
            'id'=>$id,
            'titulo' => $course[0]->titulo,
            'subtitulo' => $course[0]->subtitulo,
            'disponible' => $course[0]->disponible,
            'capitulos' => $course[0]->capitulos,
            'audio' => $course[0]->audio,
            'nivel' => $course[0]->nivel,
            'slug' => $course[0]->slug,
            'tiempovalido'=>$course[0]->tiempovalido,
            'banner'=>$course[0]->banner,
        ];

        return $datos;
    }

    public function examcourse(){
        return $this->hasOne(ExamCourse::class);
    }

    public function certification(){
        return $this->belongsTo(Certification::class);
    }

    public static function getcourse($item) {
        if (empty($item)) {
            return '';
        }

        $comp = unserialize($item);
        if (!isset($comp->items) || empty($comp->items)) {
            return '';
        }

        $key = array_keys($comp->items);
        if (empty($key)) {
            return '';
        }

        $course_id = $key[0];
        if (!isset($comp->items[$course_id]['curso'])) {
            return '';
        }

        $curso = $comp->items[$course_id]['curso'];

        return $curso;
    }
}
