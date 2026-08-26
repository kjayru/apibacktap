<?php

namespace App\Filament\Resources\ChapterQuizOptions\Pages;

use App\Filament\Resources\ChapterQuizOptions\ChapterQuizOptionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChapterQuizOption extends CreateRecord
{
    protected static string $resource = ChapterQuizOptionResource::class;

    /** El cliente pidió quitar "Create & create another" del panel. */
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->formId('form'),
            $this->getCancelFormAction()->formId('form'),
        ];
    }
}
