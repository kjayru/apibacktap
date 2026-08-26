<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\Certification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->required(),
                TextInput::make('subtitulo')
                    ->default(null),
                FileUpload::make('banner')
                    ->disk('public')
                    ->directory('banner')
                    ->image()
                    ->helperText('Medida sugerida: 1920 x 480 px.'),
                FileUpload::make('video')
                    ->disk('public')
                    ->directory('video')
                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                    ->maxSize(512000)
                    ->helperText('MP4, WebM u OGG. Peso máximo 500 MB.'),
                TextInput::make('resumen')
                    ->default(null),
                Textarea::make('contenido')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('precio')
                    ->required()
                    ->numeric(),
                TextInput::make('disponible')
                    ->default(null),
                TextInput::make('capitulos')
                    ->default(null),
                TextInput::make('audio')
                    ->default(null),
                TextInput::make('nivel')
                    ->default(null),
                TextInput::make('language')
                    ->default(null),
                TextInput::make('responsable')
                    ->default(null),
                TextInput::make('tiempovalido')
                    ->default(null),
                Select::make('certification_id')
                    ->label('Certification')
                    ->options(fn (): array => Certification::query()
                        ->orderBy('id')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->preload(),
            ]);
    }
}
