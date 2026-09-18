<?php

namespace App\Filament\Concerns;

use App\Filament\Resources\Exams\ExamResource;

/**
 * Para los listados sueltos de preguntas y opciones de examen: mezclan los de todos los
 * exámenes (#1719, #1725). Se gestionan desde su examen, así que quien llegue por URL
 * acaba en el listado de exámenes.
 */
trait RedirectsToExams
{
    public function mount(): void
    {
        $this->redirect(ExamResource::getUrl('index'));
    }
}
