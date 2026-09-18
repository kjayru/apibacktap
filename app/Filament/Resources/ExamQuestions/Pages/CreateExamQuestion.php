<?php

namespace App\Filament\Resources\ExamQuestions\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\ExamQuestions\ExamQuestionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExamQuestion extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = ExamQuestionResource::class;
}
