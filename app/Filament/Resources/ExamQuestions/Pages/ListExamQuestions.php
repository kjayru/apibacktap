<?php

namespace App\Filament\Resources\ExamQuestions\Pages;

use App\Filament\Resources\ExamQuestions\ExamQuestionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExamQuestions extends ListRecords
{
    protected static string $resource = ExamQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
