<?php

namespace App\Filament\Resources\ChapterQuizzes\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\ChapterQuizzes\ChapterQuizResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChapterQuiz extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = ChapterQuizResource::class;
}
