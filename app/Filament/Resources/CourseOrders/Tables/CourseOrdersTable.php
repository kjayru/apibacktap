<?php

namespace App\Filament\Resources\CourseOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CourseOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // El primero de la lista debe ser la última orden pagada.
            ->defaultSort('id', 'desc')
            // Sin buscador en esta sección, a petición del cliente.
            ->searchable(false)
            ->columns([
                TextColumn::make('id')
                    ->label('Order Nº')
                    ->sortable(),
                TextColumn::make('order_id')
                    ->label('Order ID'),
                TextColumn::make('name')
                    ->label('Name'),
                TextColumn::make('email')
                    ->label('Email'),
                // Esta columna guarda dos formatos según la antigüedad de la orden:
                // las recientes un JSON con course_id/slug/title, y las antiguas el
                // carrito serializado con el modelo Course entero dentro. Del
                // serializado se extrae el título por patrón: deserializar objetos
                // de la base sería innecesariamente arriesgado.
                TextColumn::make('course')
                    ->label('Product')
                    ->formatStateUsing(function ($state) {
                        $texto = (string) $state;

                        $datos = json_decode($texto, true);
                        if (is_array($datos) && isset($datos['title'])) {
                            return $datos['title'];
                        }

                        if (preg_match('/s:6:"titulo";s:\\d+:"([^"]*)"/', $texto, $m)) {
                            return $m[1];
                        }

                        return $texto;
                    }),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('txn_id')
                    ->label('Transaction'),
                TextColumn::make('cupon')
                    ->label('Coupon'),
                TextColumn::make('cupon_mount')
                    ->label('Coupon mount')
                    ->formatStateUsing(fn ($state) => filled($state) ? "{$state}%" : null),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            // Las órdenes pagadas no se editan: solo se consultan.
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
