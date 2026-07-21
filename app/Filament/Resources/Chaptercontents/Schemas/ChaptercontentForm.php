<?php

namespace App\Filament\Resources\Chaptercontents\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ChaptercontentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->default(null),
                TextInput::make('slug')
                    ->default(null),
                FileUpload::make('video')
                    ->disk('public')
                    ->directory('video')
                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                    ->required(),
                FileUpload::make('poster')
                    ->disk('public')
                    ->directory('poster')
                    ->image(),
                RichEditor::make('contenido')
                    ->label('Content')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('chapter_id')
                    ->required()
                    ->numeric(),
                FileUpload::make('audio')
                    ->disk('public')
                    ->directory('audio')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg']),
                TextInput::make('order')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
