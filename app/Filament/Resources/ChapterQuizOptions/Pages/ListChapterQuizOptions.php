<?php

namespace App\Filament\Resources\ChapterQuizOptions\Pages;

use App\Filament\Resources\ChapterQuizOptions\ChapterQuizOptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChapterQuizOptions extends ListRecords
{
    protected static string $resource = ChapterQuizOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
