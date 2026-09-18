<?php

namespace App\Filament\Support;

use App\Filament\Resources\Courses\CourseResource;
use App\Models\Chapter;
use App\Models\Course;

/**
 * Migas de pan del flujo de cursos como en el admin de producción: cada pantalla
 * cuelga de su curso y de su capítulo, y "Chapters" vuelve a los capítulos de ese
 * curso. Con las migas por defecto de Filament llevaban al listado de todos los
 * capítulos de todos los cursos (#1708, #1713).
 */
class CourseBreadcrumbs
{
    /** @return array<string, string> */
    public static function course(Course $course): array
    {
        return [
            CourseResource::getUrl('index') => CourseResource::getPluralModelLabel(),
            CourseResource::getUrl('edit', ['record' => $course]) => $course->titulo,
        ];
    }

    /** @return array<int|string, string> */
    public static function chapter(Chapter $chapter): array
    {
        return [
            ...static::course($chapter->course),
            CourseResource::getUrl('chapters', ['record' => $chapter->course]) => 'Chapters',
            $chapter->title,
        ];
    }
}
