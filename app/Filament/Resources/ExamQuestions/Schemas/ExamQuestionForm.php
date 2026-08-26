<?php

namespace App\Filament\Resources\ExamQuestions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ExamQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('exam_id')
                    ->relationship('exam', 'title')
                    ->searchable()
                    ->preload()->required(),
                Textarea::make('question')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
