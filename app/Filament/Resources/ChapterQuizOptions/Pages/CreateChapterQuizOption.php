<?php

namespace App\Filament\Resources\ChapterQuizOptions\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\ChapterQuizOptions\ChapterQuizOptionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChapterQuizOption extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = ChapterQuizOptionResource::class;
}
