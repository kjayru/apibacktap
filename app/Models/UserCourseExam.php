<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_course_id', 'exam_id', 'tiempo', 'intentos', 'resultado', 'complete', 'evento'])]
class UserCourseExam extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'user_course_id',
        'exam_id',
        'tiempo',
        'intentos',
        'resultado',
        'complete',
        'evento',
    ];
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function course(){
        return $this->belongsTo(Course::class);

    }
    public function quiz(){
        return $this->belongsTo(Quiz::class);
    }

    public function userCourseExamOptions(){
        return $this->hasMany(UserCourseExamOption::class);
    }

    public function userCourse()
    {
        return $this->belongsTo(UserCourse::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function results()
    {
        return $this->hasMany(UserCourseExamResult::class);
    }
}
