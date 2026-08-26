<?php

namespace App\Filament\Resources\CourseOrders\Schemas;

use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Schema;

class CourseOrderInfolist
{
    /**
     * La orden se lee como la factura del admin anterior, no como un volcado de campos:
     * antes se veía el carrito serializado entero, ilegible (#1417). Se añaden el cupón
     * y el descuento, que aquella pantalla no traía.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ViewEntry::make('invoice')
                    ->view('filament.course-orders.invoice')
                    ->columnSpanFull(),
            ]);
    }
}
