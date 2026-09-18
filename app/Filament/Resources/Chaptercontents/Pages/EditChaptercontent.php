<?php

namespace App\Filament\Resources\Chaptercontents\Pages;

use App\Filament\Resources\Chaptercontents\ChaptercontentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditChaptercontent extends EditRecord
{
    protected static string $resource = ChaptercontentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
