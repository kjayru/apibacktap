<?php

namespace App\Filament\Resources\ChapterQuizzes\Pages;

use App\Filament\Resources\ChapterQuizzes\ChapterQuizResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChapterQuiz extends CreateRecord
{
    protected static string $resource = ChapterQuizResource::class;

    /** El cliente pidió quitar "Create & create another" del panel. */
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->formId('form'),
            $this->getCancelFormAction()->formId('form'),
        ];
    }
}
