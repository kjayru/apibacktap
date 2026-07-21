<?php

namespace App\Filament\Resources\ExamQuestions\Pages;

use App\Filament\Resources\ExamQuestions\ExamQuestionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExamQuestion extends ViewRecord
{
    protected static string $resource = ExamQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
