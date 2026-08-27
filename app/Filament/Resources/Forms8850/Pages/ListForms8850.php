<?php

namespace App\Filament\Resources\Forms8850\Pages;

use App\Filament\Resources\Forms8850\Form8850Resource;
use Filament\Resources\Pages\ListRecords;

class ListForms8850 extends ListRecords
{
    protected static string $resource = Form8850Resource::class;

    /** No se crean a mano: llegan del formulario de la web. */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
