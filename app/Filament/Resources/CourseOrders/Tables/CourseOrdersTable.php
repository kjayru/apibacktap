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
                TextColumn::make('product_title')
                    ->label('Product'),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('txn_id')
                    ->label('Transaction'),
                TextColumn::make('cupon')
                    ->label('Coupon'),
                // Guarda el descuento en dólares, no el porcentaje del cupón.
                TextColumn::make('cupon_mount')
                    ->label('Discount')
                    ->money('USD')
                    ->placeholder('-'),
                TextColumn::make('amount')
                    ->label('Paid')
                    ->money('USD'),
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
