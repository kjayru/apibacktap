<?php

namespace App\Filament\Resources\ChapterQuizzes\Schemas;

use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ChapterQuizForm
{
    /**
     * La pregunta y sus respuestas en la misma pantalla, como "Create Question" en
     * producción. El capítulo no se elige aquí: la pregunta se crea desde el quiz de su
     * capítulo. Tampoco el selector "Quiz", que no corresponde a nada (#1733), ni la
     * tabla de opciones de abajo, que era una segunda forma de añadir respuestas (#1707).
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('question')
                    ->label('Question')
                    ->required()
                    ->columnSpanFull(),
                Repeater::make('options')
                    ->relationship()
                    ->label('Answers')
                    ->addActionLabel('Add option')
                    ->reorderable(false)
                    ->columns(4)
                    ->columnSpanFull()
                    ->rules([
                        // Sin respuesta correcta el alumno no puede aprobar nunca el quiz (#1711).
                        fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                            if (! collect($value)->contains(fn (array $option): bool => (bool) ($option['estado'] ?? false))) {
                                $fail('Mark which answer is the correct one.');
                            }
                        },
                    ])
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
