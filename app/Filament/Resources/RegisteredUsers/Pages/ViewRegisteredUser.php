<?php

namespace App\Filament\Resources\RegisteredUsers\Pages;

use App\Filament\Resources\RegisteredUsers\RegisteredUsersResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRegisteredUser extends ViewRecord
{
    protected static string $resource = RegisteredUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
