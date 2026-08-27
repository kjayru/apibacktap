<?php

namespace App\Filament\Resources\Chapters\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ChapterForm
{
    /**
     * Vídeo, audio, lectura y preguntas son marcas de qué lleva el capítulo, no textos:
     * van con casilla, como en el admin anterior (#1566). El material en sí vive en los
     * contenidos del capítulo, así que aquí no hay más que el título y esas marcas.
     *
     * Se usa igual desde el capítulo y desde el curso; allí el curso ya viene dado por
     * la relación y el selector se omite.
     */
    public static function configure(Schema $schema, bool $withCourse = true): Schema
    {
        return $schema->components(static::fields($withCourse));
    }

    /** @return array<int, mixed> */
    public static function fields(bool $withCourse = true): array
    {
        return array_values(array_filter([
            TextInput::make('title')
                ->label('Title')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            $withCourse
                ? Select::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'titulo')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull()
                : null,
            static::flag('video', 'Video'),
            static::flag('audio', 'Audio'),
            static::flag('reading', 'Reading'),
            static::flag('quiz', 'Questions about the chapter'),
            TextInput::make('order')
                ->label('Order')
                ->numeric()
                ->required()
                ->columnSpanFull(),
        ]));
    }

    /**
     * Las columnas guardan '1' o NULL, y la API distingue con filled(): un false de PHP
     * se escribiría como '0' y la lectura daría "sí tiene vídeo" en todos los capítulos.
     * Por eso la casilla desmarcada vuelve a NULL y no a false.
     */
    public static function flag(string $name, string $label): Checkbox
    {
        return Checkbox::make($name)
            ->label($label)
            ->formatStateUsing(fn ($state): bool => filled($state) && $state !== '0')
            ->dehydrateStateUsing(fn ($state): ?int => $state ? 1 : null);
    }
}
