<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourseExamOption extends Model
{
    use HasFactory;

    public function userCourseExam(){
        return $this->belongsTo(UserCourseExam::class);
    }
}
