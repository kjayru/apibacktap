<?php

namespace App\Filament\Resources\Forms8850\Pages;

use App\Filament\Resources\Forms8850\Form8850Resource;
use Filament\Resources\Pages\ViewRecord;

class ViewForm8850 extends ViewRecord
{
    protected static string $resource = Form8850Resource::class;

    /** Un envío recibido no se edita. */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
