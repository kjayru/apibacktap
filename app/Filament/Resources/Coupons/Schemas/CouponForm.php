<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('cupon')
                    ->required(),
                TextInput::make('monto_descuento')
                    ->required(),
                TextInput::make('estado')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
