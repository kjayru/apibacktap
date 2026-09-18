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
                // Sin type="number": el navegador deja escribir la "e" de la notación
                // científica (#1657). La máscara sólo admite cifras al teclear.
                TextInput::make('monto_descuento')
                    ->label('Discount mount %')
                    ->inputMode('numeric')
                    ->mask('999')
                    // La regla y no ->integer(), que vuelve a poner type="number".
                    ->rule('integer')
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
