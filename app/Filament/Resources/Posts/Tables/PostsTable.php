<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostsTable
{
    /**
     * Sin buscador ni filtros (#1489). Se corrige la cabecera de la primera columna
     * (#1490), se recupera "Resume" del admin anterior (#1492) y se añade "delete" (#1493).
     */
    public static function configure(Table $table): Table
    {
        return $table
            // El primero de la lista debe ser el último registro creado.
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('titulo')
                    ->label('Title')
                    ->wrap(),
                TextColumn::make('resumen')
                    ->label('Resume')
                    ->limit(80)
                    ->wrap()
                    ->placeholder('-'),
                ImageColumn::make('card')
                    ->label('Card')
                    ->disk('public')
                    ->height(60),
                ImageColumn::make('banner')
                    ->label('Banner')
                    ->disk('public')
                    ->height(60),
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
