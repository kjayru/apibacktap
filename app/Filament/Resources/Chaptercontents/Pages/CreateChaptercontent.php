<?php

namespace App\Filament\Resources\Chaptercontents\Pages;

use App\Filament\Resources\Chaptercontents\ChaptercontentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChaptercontent extends CreateRecord
{
    protected static string $resource = ChaptercontentResource::class;

    /** El cliente pidió quitar "Create & create another" del panel. */
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->formId('form'),
            $this->getCancelFormAction()->formId('form'),
        ];
    }
}
