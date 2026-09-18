<?php

namespace App\Filament\Resources\Chapters\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\Chapters\ChapterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChapter extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = ChapterResource::class;
}
