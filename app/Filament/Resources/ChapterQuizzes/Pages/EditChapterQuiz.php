<?php

namespace App\Filament\Resources\ChapterQuizzes\Pages;

use App\Filament\Resources\ChapterQuizzes\ChapterQuizResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditChapterQuiz extends EditRecord
{
    protected static string $resource = ChapterQuizResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
