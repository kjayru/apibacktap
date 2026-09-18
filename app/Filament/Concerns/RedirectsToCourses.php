<?php

namespace App\Filament\Concerns;

use App\Filament\Resources\Courses\CourseResource;

/**
 * Para los listados sueltos de capítulos, contenidos, preguntas y opciones: mezclan
 * los de todos los cursos y el cliente no debe verlos (#1713). Todo eso se gestiona
 * desde su curso, así que quien llegue por URL acaba en el listado de cursos.
 */
trait RedirectsToCourses
{
    public function mount(): void
    {
        $this->redirect(CourseResource::getUrl('index'));
    }
}
