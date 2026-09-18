<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = UserResource::class;
}
