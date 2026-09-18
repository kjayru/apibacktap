<?php

namespace App\Filament\Resources\Industries\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\Industries\IndustryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIndustry extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = IndustryResource::class;
}
