<?php

namespace App\Filament\Resources\UsersTaking\Pages;

use App\Filament\Resources\UsersTaking\UsersTakingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUsersTaking extends EditRecord
{
    protected static string $resource = UsersTakingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
