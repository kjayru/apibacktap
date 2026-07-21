<?php

namespace App\Filament\Resources\ExamQuestionOptions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExamQuestionOptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('exam_question_id')
                    ->required()
                    ->numeric(),
                TextInput::make('opcion')
                    ->required(),
                TextInput::make('resultado')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
