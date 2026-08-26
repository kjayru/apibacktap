<?php

namespace App\Filament\Resources\Chaptercontents\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
                    ->maxSize(512000)
                    ->helperText('MP4, WebM u OGG. Peso máximo 500 MB.')
                    ->required(),
                FileUpload::make('poster')
                    ->disk('public')
                    ->directory('poster')
                    ->image()
                    ->helperText('Medida sugerida: 1280 x 720 px.'),
                RichEditor::make('contenido')
                    ->label('Content')
                    ->required()
                    ->columnSpanFull(),
                Select::make('chapter_id')
                    ->relationship('chapter', 'title')
                    ->searchable()
                    ->preload()->required(),
                FileUpload::make('audio')
                    ->disk('public')
                    ->directory('audio')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg'])
                    ->maxSize(512000)
                    ->helperText('MP3, WAV u OGG. Peso máximo 500 MB.'),
                TextInput::make('order')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
