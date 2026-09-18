<?php

namespace App\Filament\Resources\Information\Pages;

use App\Filament\Resources\Information\InformationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewInformation extends ViewRecord
{
    protected static string $resource = InformationResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    /**
     * Referencias y empleos ya salen completos en sus bloques de la ficha; las tablas de
     * abajo los repetían (#1447, #1667). En la edición se mantienen para poder cambiarlos.
     */
    public function getRelationManagers(): array
    {
        return [];
    }
}
