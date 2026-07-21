<?php

namespace App\Filament\Resources\UsersTaking\Pages;

use App\Filament\Resources\UsersTaking\UsersTakingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUsersTaking extends ViewRecord
{
    protected static string $resource = UsersTakingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
