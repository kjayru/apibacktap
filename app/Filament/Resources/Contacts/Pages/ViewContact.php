<?php

namespace App\Filament\Resources\Contacts\Pages;

use App\Filament\Resources\Contacts\ContactResource;
use Filament\Resources\Pages\ViewRecord;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    /** Un mensaje recibido no se edita (#1463). */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
