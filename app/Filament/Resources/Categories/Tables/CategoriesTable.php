<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    /**
     * Sin buscador ni filtros (#1464) y sin la columna "Parent id" (#1466): son pocas
     * categorías y el listado cabe entero en pantalla. Se añade "delete" (#1467).
     */
    public static function configure(Table $table): Table
    {
        return $table
            // El primero de la lista debe ser el último registro creado.
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Name'),
                TextColumn::make('slug')
                    ->label('Slug'),
                ImageColumn::make('card')
                    ->label('Card')
                    ->disk('public')
                    ->height(60),
                ImageColumn::make('banner')
                    ->label('Banner')
                    ->disk('public')
                    ->height(60),
                TextColumn::make('orden')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
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
