<?php

namespace App\Filament\Resources\ExamQuestionOptions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExamQuestionOptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('exam_question_id')
                    ->relationship('examquestion', 'question')
                    ->searchable()
                    ->preload()->required(),
                TextInput::make('opcion')
                    ->required(),
                TextInput::make('resultado')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
