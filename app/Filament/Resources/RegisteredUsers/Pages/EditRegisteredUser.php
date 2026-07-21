<?php

namespace App\Filament\Resources\RegisteredUsers\Pages;

use App\Filament\Resources\RegisteredUsers\RegisteredUsersResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRegisteredUser extends EditRecord
{
    protected static string $resource = RegisteredUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
