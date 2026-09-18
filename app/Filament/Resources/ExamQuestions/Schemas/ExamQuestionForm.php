<?php

namespace App\Filament\Resources\ExamQuestions\Schemas;

use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExamQuestionForm
{
    /**
     * La pregunta y sus opciones en la misma pantalla, como en el admin de producción
     * (#1619, #1728). El examen no se elige aquí porque la pregunta se crea desde las
     * preguntas de su examen; y la tabla de opciones de abajo, que era una segunda forma
     * de añadirlas, desaparece (#1722).
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('question')
                    ->label('Question')
                    ->required()
                    ->columnSpanFull(),
                Repeater::make('examquestionoptions')
                    ->relationship()
                    ->label('Answers')
                    ->addActionLabel('Add option')
                    ->reorderable(false)
                    ->columns(4)
                    ->columnSpanFull()
                    ->rules([
                        // Sin respuesta correcta la pregunta no puntúa para nadie.
                        fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                            if (! collect($value)->contains(fn (array $option): bool => (bool) ($option['resultado'] ?? false))) {
                                $fail('Mark which answer is the correct one.');
                            }
                        },
                    ])
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
