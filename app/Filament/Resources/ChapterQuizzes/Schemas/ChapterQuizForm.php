<?php

namespace App\Filament\Resources\ChapterQuizzes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ChapterQuizForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('chapter_id')
                    ->required()
                    ->numeric(),
                TextInput::make('quiz_id')
                    ->numeric()
                    ->default(null),
                Textarea::make('question')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
