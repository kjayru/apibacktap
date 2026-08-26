<?php

namespace App\Filament\Resources\ExamQuestionOptions\Pages;

use App\Filament\Resources\ExamQuestionOptions\ExamQuestionOptionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExamQuestionOption extends CreateRecord
{
    protected static string $resource = ExamQuestionOptionResource::class;

    /** El cliente pidió quitar "Create & create another" del panel. */
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->formId('form'),
            $this->getCancelFormAction()->formId('form'),
        ];
    }
}
