<?php

namespace App\Filament\Resources\ChapterQuizzes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ChapterQuizForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('chapter_id')
                    ->relationship('chapter', 'title')
                    ->searchable()
                    ->preload()->required(),
                Select::make('quiz_id')
                    ->relationship('quiz', 'title')
                    ->searchable()
                    ->preload(),
                Textarea::make('question')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
