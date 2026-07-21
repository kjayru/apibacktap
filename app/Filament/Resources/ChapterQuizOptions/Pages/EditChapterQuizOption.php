<?php

namespace App\Filament\Resources\ChapterQuizOptions\Pages;

use App\Filament\Resources\ChapterQuizOptions\ChapterQuizOptionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditChapterQuizOption extends EditRecord
{
    protected static string $resource = ChapterQuizOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
