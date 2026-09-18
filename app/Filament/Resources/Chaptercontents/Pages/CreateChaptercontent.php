<?php

namespace App\Filament\Resources\Chaptercontents\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\Chaptercontents\ChaptercontentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChaptercontent extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = ChaptercontentResource::class;
}
