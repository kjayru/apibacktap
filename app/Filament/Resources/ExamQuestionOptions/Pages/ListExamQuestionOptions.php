<?php

namespace App\Filament\Resources\ExamQuestionOptions\Pages;

use App\Filament\Concerns\RedirectsToExams;
use App\Filament\Resources\ExamQuestionOptions\ExamQuestionOptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExamQuestionOptions extends ListRecords
{
    use RedirectsToExams;

    protected static string $resource = ExamQuestionOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
