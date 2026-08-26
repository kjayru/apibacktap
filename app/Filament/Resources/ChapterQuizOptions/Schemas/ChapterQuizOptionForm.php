<?php

namespace App\Filament\Resources\ChapterQuizOptions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ChapterQuizOptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('chapter_quiz_id')
                    ->relationship('chapterquiz', 'question')
                    ->searchable()
                    ->preload()->required(),
                Textarea::make('option')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('estado')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
