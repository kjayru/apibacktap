<?php

namespace App\Filament\Resources\Information\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\Information\InformationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInformation extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = InformationResource::class;
}
