<?php

namespace App\Filament\Resources\ChapterQuizzes\Pages;

use App\Filament\Concerns\RedirectsToCourses;
use App\Filament\Resources\ChapterQuizzes\ChapterQuizResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChapterQuizzes extends ListRecords
{
    use RedirectsToCourses;

    protected static string $resource = ChapterQuizResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
