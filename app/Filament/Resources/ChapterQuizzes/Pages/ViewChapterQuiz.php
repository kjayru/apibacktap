<?php

namespace App\Filament\Resources\ChapterQuizzes\Pages;

use App\Filament\Resources\ChapterQuizzes\ChapterQuizResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewChapterQuiz extends ViewRecord
{
    protected static string $resource = ChapterQuizResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
