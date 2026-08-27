<?php

namespace App\Filament\Resources\UsersTaking\Pages;

use App\Filament\Resources\Users\Concerns\SyncsUserFromProfile;
use App\Filament\Resources\UsersTaking\UsersTakingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUsersTaking extends EditRecord
{
    use SyncsUserFromProfile;

    protected static string $resource = UsersTakingResource::class;

    /** Sin "view": el listado ya no lo ofrece (#1630). */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
