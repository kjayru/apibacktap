<?php

namespace App\Filament\Resources\Coupons\Tables;

use App\Models\CourseOrder;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // El primero de la lista debe ser el último registro creado.
            ->defaultSort('id', 'desc')
            ->columns([
                // Numeración de la lista, como en producción (#1652).
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('cupon')
                    ->label('Coupon')
                    ->searchable(),
                TextColumn::make('monto_descuento')
                    ->label('Discount')
                    ->formatStateUsing(fn ($state) => filled($state) ? "{$state}%" : null)
                    ->sortable(),
                TextColumn::make('estado')
                    ->label('State')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => (int) $state === 1 ? 'Active' : 'Inactive')
                    ->color(fn ($state): string => (int) $state === 1 ? 'success' : 'gray'),
                // El uso no se guarda con una clave foránea: la orden apunta al
                // cupón por su código, así que se cuenta por ahí.
                TextColumn::make('used')
                    ->label('Used')
                    ->state(fn ($record): int => CourseOrder::where('cupon', $record->cupon)->count()),
                TextColumn::make('created_at')
                    ->label('Created')
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
