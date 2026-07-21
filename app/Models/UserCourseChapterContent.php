<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_course_chapter_id', 'content_id'])]
class UserCourseChapterContent extends Model
{
    use HasFactory;

    public function userCourseChapter(){
        return $this->BelongsTo(UserCourseChapter::class);
    }
}
