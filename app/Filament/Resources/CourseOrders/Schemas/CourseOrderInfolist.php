<?php

namespace App\Filament\Resources\CourseOrders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CourseOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('course')
                    ->columnSpanFull(),
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('price')
                    ->placeholder('-'),
                TextEntry::make('order_id'),
                TextEntry::make('currency'),
                TextEntry::make('amount')
                    ->placeholder('-'),
                TextEntry::make('txn_id')
                    ->placeholder('-'),
                TextEntry::make('checkout_session_id'),
                TextEntry::make('payment_status'),
                TextEntry::make('cupon')
                    ->placeholder('-'),
                TextEntry::make('cupon_mount')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
