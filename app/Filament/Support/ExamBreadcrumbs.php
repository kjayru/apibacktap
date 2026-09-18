<?php

namespace App\Filament\Support;

use App\Filament\Resources\Exams\ExamResource;
use App\Models\Exam;

/**
 * Migas de pan de las preguntas de un examen: "Exam question" vuelve a las preguntas
 * de ese examen y no al listado global de todos los exámenes (#1725).
 */
class ExamBreadcrumbs
{
    /** @return array<string, string> */
    public static function exam(Exam $exam): array
    {
        return [
            ExamResource::getUrl('index') => ExamResource::getPluralModelLabel(),
            ExamResource::getUrl('edit', ['record' => $exam]) => $exam->title,
        ];
    }

    /** @return array<string, string> */
    public static function questions(Exam $exam): array
    {
        return [
            ...static::exam($exam),
            ExamResource::getUrl('questions', ['record' => $exam]) => 'Exam question',
        ];
    }
}
