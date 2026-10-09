<?php

namespace App\Filament\Resources\EventOrders;

use App\Filament\Resources\EventOrders\Pages\ListEventOrders;
use App\Filament\Resources\EventOrders\Tables\EventOrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Los cursos presenciales se cobran por su propia pasarela y se guardan en otra tabla, así
 * que no salían en el listado de órdenes y parecía que el pago se había perdido (#1811).
 * Aquí se consultan, junto a las órdenes de los cursos online.
 */
class EventOrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $navigationLabel = 'In-person training orders';

    protected static ?string $modelLabel = 'In-person training order';

    protected static ?string $pluralModelLabel = 'In-person training orders';

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?string $slug = 'in-person-orders';

    protected static ?int $navigationSort = 21;

    public static function table(Table $table): Table
    {
        return EventOrdersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventOrders::route('/'),
        ];
    }
}
