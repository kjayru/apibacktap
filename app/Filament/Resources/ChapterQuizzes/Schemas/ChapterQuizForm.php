<?php

namespace App\Filament\Resources\ChapterQuizzes\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ChapterQuizForm
{
    /**
     * Mismo criterio que en las preguntas de examen: las respuestas se añaden y se
     * marcan aquí, sin pantallas intermedias, como pide #1597.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('chapter_id')
                    ->label('Chapter')
                    ->relationship('chapter', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('quiz_id')
                    ->label('Quiz')
                    ->relationship('quiz', 'title')
                    ->searchable()
                    ->preload(),
                Textarea::make('question')
                    ->label('Questions about the chapter')
                    ->required()
                    ->columnSpanFull(),
                Repeater::make('options')
                    ->relationship()
                    ->label('Answers')
                    ->addActionLabel('Add option')
                    ->reorderable(false)
                    ->columns(4)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('option')
                            ->label('Answer')
                            ->required()
                            ->columnSpan(3),
                        Toggle::make('estado')
                            ->label('Correct')
                            ->inline(false),
                    ]),
            ]);
    }
}
