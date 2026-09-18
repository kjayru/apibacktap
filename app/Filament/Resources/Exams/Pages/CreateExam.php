<?php

namespace App\Filament\Resources\Exams\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\Exams\ExamResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExam extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = ExamResource::class;
}
