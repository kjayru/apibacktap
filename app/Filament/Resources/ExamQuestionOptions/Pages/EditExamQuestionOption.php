<?php

namespace App\Filament\Resources\ExamQuestionOptions\Pages;

use App\Filament\Resources\ExamQuestionOptions\ExamQuestionOptionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditExamQuestionOption extends EditRecord
{
    protected static string $resource = ExamQuestionOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
