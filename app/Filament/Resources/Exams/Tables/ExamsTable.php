<?php

namespace App\Filament\Resources\Exams\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // El primero de la lista debe ser el último registro creado.
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Exam name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('exam_questions_count')
                    ->label('Questions')
                    ->counts('examquestions'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('questions')
                    ->label('Questions')
                    ->icon('heroicon-o-queue-list')
                    ->color('warning')
                    ->url(fn ($record): string => route('filament.admin.resources.exam-questions.index', [
                        'tableSearch' => $record->title,
                    ])),
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
