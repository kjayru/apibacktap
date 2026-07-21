<?php

namespace App\Filament\Resources\ExamQuestionOptions\Pages;

use App\Filament\Resources\ExamQuestionOptions\ExamQuestionOptionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExamQuestionOption extends ViewRecord
{
    protected static string $resource = ExamQuestionOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
