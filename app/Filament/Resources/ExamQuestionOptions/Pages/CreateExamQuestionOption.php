<?php

namespace App\Filament\Resources\ExamQuestionOptions\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\ExamQuestionOptions\ExamQuestionOptionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExamQuestionOption extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = ExamQuestionOptionResource::class;
}
