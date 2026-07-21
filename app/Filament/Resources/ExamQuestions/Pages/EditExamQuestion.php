<?php

namespace App\Filament\Resources\ExamQuestions\Pages;

use App\Filament\Resources\ExamQuestions\ExamQuestionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditExamQuestion extends EditRecord
{
    protected static string $resource = ExamQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
