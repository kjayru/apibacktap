<?php

namespace App\Filament\Resources\ChapterQuizOptions\Pages;

use App\Filament\Resources\ChapterQuizOptions\ChapterQuizOptionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewChapterQuizOption extends ViewRecord
{
    protected static string $resource = ChapterQuizOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
