<?php

namespace App\Filament\Resources\Certifications\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\Certifications\CertificationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCertification extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = CertificationResource::class;
}
