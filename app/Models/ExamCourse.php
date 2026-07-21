<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamCourse extends Model
{
    use HasFactory;

    protected $fillable = ['exam_id','course_id'];
    public function exam(){
        return $this->belongsTo(Exam::class);

    }

    public function course(){
        return $this->belongsTo(Course::class);
    }
}
