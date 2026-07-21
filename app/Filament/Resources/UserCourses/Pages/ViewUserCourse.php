<?php

namespace App\Filament\Resources\UserCourses\Pages;

use App\Filament\Resources\UserCourses\UserCourseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUserCourse extends ViewRecord
{
    protected static string $resource = UserCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
