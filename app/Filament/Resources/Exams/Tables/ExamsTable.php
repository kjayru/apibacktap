<?php

namespace App\Filament\Resources\Exams\Tables;

use App\Filament\Resources\Exams\ExamResource;
use App\Models\Exam;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExamsTable
{
    /**
     * Como el listado de producción: título, columna "Options" con el botón "Options
     * exam" que lleva a las preguntas de ese examen, y fecha (#1608).
     */
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
                TextColumn::make('examquestions_count')
                    ->label('Options')
                    ->counts('examquestions')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state): string => "Options exam · {$state}")
                    ->url(fn (Exam $record): string => ExamResource::getUrl('questions', ['record' => $record])),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
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
