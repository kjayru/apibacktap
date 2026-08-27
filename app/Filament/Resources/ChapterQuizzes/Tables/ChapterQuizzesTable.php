<?php

namespace App\Filament\Resources\ChapterQuizzes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChapterQuizzesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // El primero de la lista debe ser el último registro creado.
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('chapter.title')
                    ->label('Chapter')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('question')
                    ->label('Question')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('correct')
                    ->label('Correct answer')
                    ->placeholder('-')
                    ->state(fn ($record) => $record->options
                        ->firstWhere('estado', 1)?->option),
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
