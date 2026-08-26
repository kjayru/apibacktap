<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestionOption extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'exam_question_id',
        'opcion',
        'resultado',
    ];
    use HasFactory;

    public function examquestion(){
        return $this->belongsTo(ExamQuestion::class, 'exam_question_id');
    }
}
