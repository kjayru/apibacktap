<?php

namespace App\Filament\Resources\ExamQuestions\Pages;

use App\Filament\Resources\ExamQuestions\ExamQuestionResource;
use App\Filament\Resources\Exams\ExamResource;
use App\Filament\Support\ExamBreadcrumbs;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

/**
 * Pregunta y opciones en una sola pantalla, como "Edit Question" en producción (#1619,
 * #1728); al guardar o borrar, de vuelta a las preguntas de su examen (#1723).
 */
class EditExamQuestion extends EditRecord
{
    protected static string $resource = ExamQuestionResource::class;

    protected static ?string $title = 'Edit Question';

    public function getBreadcrumbs(): array
    {
        return [...ExamBreadcrumbs::questions($this->getRecord()->exam), 'Edit'];
    }

    protected function getHeaderActions(): array
    {
        return [
            // La URL se calcula aquí, antes de borrar: evaluada después, la pregunta ya no
            // existe y la petición acababa en 404 sin redirigir.
            DeleteAction::make()
                ->successRedirectUrl($this->questionsUrl()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->questionsUrl();
    }

    private function questionsUrl(): string
    {
        return ExamResource::getUrl('questions', ['record' => $this->getRecord()->exam_id]);
    }
}
