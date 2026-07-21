<?php

namespace App\Filament\Resources\UserCourses\Pages;

use App\Filament\Resources\UserCourses\UserCourseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserCourses extends ListRecords
{
    protected static string $resource = UserCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
