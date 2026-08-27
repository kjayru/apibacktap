<?php

namespace App\Filament\Resources\RegisteredUsers\Pages;

use App\Filament\Resources\RegisteredUsers\RegisteredUsersResource;
use App\Filament\Resources\Users\Concerns\SyncsUserFromProfile;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRegisteredUser extends EditRecord
{
    use SyncsUserFromProfile;

    protected static string $resource = RegisteredUsersResource::class;

    /** Sin "view": el listado ya no lo ofrece (#1642). */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
