<?php

namespace App\Filament\Resources\EventOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventOrdersTable
{
    /** Las mismas columnas que el listado de órdenes online, con la fecha de Texas (#1810). */
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->searchable(false)
            ->columns([
                TextColumn::make('id')
                    ->label('Order Nº')
                    ->sortable(),
                TextColumn::make('item_number')
                    ->label('Order ID')
                    ->placeholder('-'),
                TextColumn::make('name')
                    ->label('Name'),
                TextColumn::make('email')
                    ->label('Email'),
                TextColumn::make('item_name')
                    ->label('Training'),
                TextColumn::make('item_price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('txn_id')
                    ->label('Transaction')
                    ->placeholder('-'),
                TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'succeeded' ? 'success' : 'warning'),
                TextColumn::make('paid_amount')
                    ->label('Paid')
                    ->money('USD')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y H:i')
                    ->timezone(config('app.admin_timezone'))
                    ->sortable(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
