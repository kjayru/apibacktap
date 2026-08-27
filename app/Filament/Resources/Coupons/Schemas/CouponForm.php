<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // El código lo genera el sistema al crear y no se toca al editar.
                TextInput::make('cupon')
                    ->label('Coupon')
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit'),
                TextInput::make('monto_descuento')
                    ->label('Discount mount %')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->suffix('%')
                    ->required(),
                Toggle::make('estado')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
