<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // El primero de la lista debe ser el último registro creado.
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Start date')
                    ->date('M d, Y')
                    // Se busca por lo que se ve en la tabla ("Dec 16, 2023") y también por
                    // el valor guardado ("2023-12-16"). Comparar sólo contra el valor
                    // guardado no encontraba nada escribiendo la fecha como aparece (#1408).
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->where(fn (Builder $query): Builder => $query
                            ->whereRaw("DATE_FORMAT(start_date, '%b %d, %Y') LIKE ?", ["%{$search}%"])
                            ->orWhere('start_date', 'like', "%{$search}%")))
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->searchable()
                    ->sortable(),
            ])
            // Sin esto se lanzaba una consulta por cada tecla.
            ->searchDebounce('500ms')
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
