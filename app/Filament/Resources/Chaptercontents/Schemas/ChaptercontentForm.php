<?php

namespace App\Filament\Resources\Chaptercontents\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ChaptercontentForm
{
    /**
     * Mismos campos y mismo orden que el admin anterior, con "Order" antes del contenido
     * (#1575). Todo es obligatorio salvo el audio, que no todos los contenidos llevan
     * (#1579).
     *
     * Se usa igual desde la sección Chaptercontent y desde el capítulo: allí el capítulo
     * ya viene dado por la relación, así que el selector se omite.
     */
    public static function configure(Schema $schema, bool $withChapter = true): Schema
    {
        return $schema->components(static::fields($withChapter));
    }

    /** @return array<int, mixed> */
    public static function fields(bool $withChapter = true): array
    {
        return array_values(array_filter([
            TextInput::make('titulo')
                ->label('Title')
                ->required()
                ->maxLength(255),
            FileUpload::make('video')
                ->label('Video')
                ->disk('public')
                ->directory('video')
                ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                ->maxSize(512000)
                ->helperText('MP4, WebM or OGG. Maximum size 500 MB.')
                ->required(),
            FileUpload::make('poster')
                ->label('Poster video')
                ->disk('public')
                ->directory('poster')
                ->image()
                ->helperText('Suggested size: 1280 x 720 px.')
                ->required(),
            FileUpload::make('audio')
                ->label('Audio')
                ->disk('public')
                ->directory('audio')
                ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg'])
                ->maxSize(512000)
                ->helperText('MP3, WAV or OGG. Maximum size 500 MB. Optional.'),
            $withChapter
                ? Select::make('chapter_id')
                    ->label('Chapter')
                    ->relationship('chapter', 'title')
                    ->searchable()
                    ->preload()
                    ->required()
                : null,
            TextInput::make('order')
                ->label('Order')
                ->numeric()
                ->required(),
            RichEditor::make('contenido')
                ->label('Content')
                ->required()
                ->columnSpanFull(),
        ]));
    }
}
