<?php

namespace App\Filament\Resources\Information\Pages;

use App\Filament\Resources\Information\InformationResource;
use Filament\Resources\Pages\ListRecords;

class ListInformation extends ListRecords
{
    protected static string $resource = InformationResource::class;

    protected function getHeaderActions(): array
    {
        // Los aspirantes llegan por el formulario público, no se crean a mano.
        return [];
    }
}
