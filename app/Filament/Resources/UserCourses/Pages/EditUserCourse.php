<?php

namespace App\Filament\Resources\UserCourses\Pages;

use App\Filament\Resources\UserCourses\UserCourseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserCourse extends EditRecord
{
    protected static string $resource = UserCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
