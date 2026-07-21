<?php

namespace App\Filament\Resources\Chapters\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ChapterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('contenido')
                    ->default(null),
                TextInput::make('course_id')
                    ->required()
                    ->numeric(),
                TextInput::make('slug')
                    ->default(null),
                TextInput::make('video')
                    ->default(null),
                Textarea::make('reading')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('audio')
                    ->default(null),
                TextInput::make('quiz')
                    ->numeric()
                    ->default(null),
                TextInput::make('order')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
