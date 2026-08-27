<?php

namespace App\Filament\Resources\Industries\Pages;

use App\Filament\Resources\Industries\IndustryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIndustry extends CreateRecord
{
    protected static string $resource = IndustryResource::class;

    /** El cliente pidió quitar "Create & create another" del panel. */
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->formId('form'),
            $this->getCancelFormAction()->formId('form'),
        ];
    }
}
