<?php

namespace App\Filament\Resources\Chaptercontents\Pages;

use App\Filament\Resources\Chaptercontents\ChaptercontentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChaptercontents extends ListRecords
{
    protected static string $resource = ChaptercontentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Nombre del botón como en producción (#1581).
            CreateAction::make()
                ->label('Create Content'),
        ];
    }
}
