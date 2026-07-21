<?php

namespace App\Filament\Resources\ExamQuestions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ExamQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('exam_id')
                    ->required()
                    ->numeric(),
                Textarea::make('question')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
