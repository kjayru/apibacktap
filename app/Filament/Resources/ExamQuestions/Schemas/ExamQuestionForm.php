<?php

namespace App\Filament\Resources\ExamQuestions\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExamQuestionForm
{
    /**
     * Las opciones se editan aquí mismo, sin abrir una pantalla aparte por cada una.
     * El tablero (#1619) pide el flujo del admin anterior: añadir respuestas y marcar
     * la correcta en el mismo formulario de la pregunta.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('exam_id')
                    ->label('Exam')
                    ->relationship('exam', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('question')
                    ->label('Exam question')
                    ->required()
                    ->columnSpanFull(),
                Repeater::make('examquestionoptions')
                    ->relationship()
                    ->label('Exam question options')
                    ->addActionLabel('Add option')
                    ->reorderable(false)
                    ->columns(4)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('opcion')
                            ->label('Answer')
                            ->required()
                            ->columnSpan(3),
                        Toggle::make('resultado')
                            ->label('Correct')
                            ->inline(false),
                    ]),
            ]);
    }
}
