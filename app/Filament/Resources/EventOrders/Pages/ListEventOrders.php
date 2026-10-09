<?php

namespace App\Filament\Resources\EventOrders\Pages;

use App\Filament\Resources\EventOrders\EventOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListEventOrders extends ListRecords
{
    protected static string $resource = EventOrderResource::class;
}
