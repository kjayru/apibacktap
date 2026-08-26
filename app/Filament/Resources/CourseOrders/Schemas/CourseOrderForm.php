<?php

namespace App\Filament\Resources\CourseOrders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CourseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('course')
                    ->required()
                    ->columnSpanFull(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('price')
                    ->default(null),
                TextInput::make('order_id')
                    ->required(),
                TextInput::make('currency')
                    ->required(),
                TextInput::make('amount')
                    ->default(null),
                TextInput::make('txn_id')
                    ->default(null),
                TextInput::make('checkout_session_id')
                    ->required(),
                TextInput::make('payment_status')
                    ->required(),
                TextInput::make('cupon')
                    ->default(null),
                TextInput::make('cupon_mount')
                    ->default(null),
            ]);
    }
}
