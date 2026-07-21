<?php

namespace App\Filament\Resources\Certifications\Pages;

use App\Filament\Resources\Certifications\CertificationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCertification extends ViewRecord
{
    protected static string $resource = CertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
