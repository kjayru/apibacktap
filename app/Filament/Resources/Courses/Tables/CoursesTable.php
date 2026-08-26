<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // El primero de la lista debe ser el último registro creado.
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('titulo')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('banner')
                    ->label('Banner')
                    ->disk('public'),
                TextColumn::make('resumen')
                    ->label('Excerpt')
                    ->wrap()
                    ->limit(90),
                TextColumn::make('precio')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('chapters_count')
                    ->label('Chapters')
                    ->counts('chapters'),
                TextColumn::make('nivel')
                    ->label('Level'),
                TextColumn::make('certification.name')
                    ->label('Certificate')
                    ->placeholder('-'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('chapters')
                    ->label('Chapters')
                    ->icon('heroicon-o-book-open')
                    ->color('warning')
                    ->url(fn (Model $record): string => route('filament.admin.resources.chapters.index', [
                        'tableSearch' => $record->titulo,
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
