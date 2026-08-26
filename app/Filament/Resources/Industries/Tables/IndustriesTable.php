<?php

namespace App\Filament\Resources\Industries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IndustriesTable
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
                ImageColumn::make('card')
                    ->label('Card')
                    ->disk('public'),
                ImageColumn::make('banner')
                    ->label('Banner')
                    ->disk('public'),
                TextColumn::make('Category.name')
                    ->label('Category')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
