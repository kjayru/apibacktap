<?php

namespace App\Filament\Resources\ExamQuestions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExamQuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // El primero de la lista debe ser el último registro creado.
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('exam.title')
                    ->label('Exam')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('question')
                    ->label('Exam question')
                    ->wrap()
                    ->searchable(),
                // La columna de opciones se sustituye por la respuesta correcta.
                TextColumn::make('correct')
                    ->label('Correct answer')
                    ->placeholder('-')
                    ->state(fn ($record) => $record->examquestionoptions
                        ->firstWhere('resultado', 1)?->opcion),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
