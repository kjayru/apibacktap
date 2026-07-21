<?php

namespace App\Filament\Resources\RegisteredUsers\Pages;

use App\Filament\Resources\RegisteredUsers\RegisteredUsersResource;
use Filament\Resources\Pages\ListRecords;

class ListRegisteredUsers extends ListRecords
{
    protected static string $resource = RegisteredUsersResource::class;
}
