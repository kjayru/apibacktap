<?php

namespace App\Filament\Resources\Chaptercontents\Pages;

use App\Filament\Resources\Chaptercontents\ChaptercontentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewChaptercontent extends ViewRecord
{
    protected static string $resource = ChaptercontentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
