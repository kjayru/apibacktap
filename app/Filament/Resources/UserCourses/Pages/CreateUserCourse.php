<?php

namespace App\Filament\Resources\UserCourses\Pages;

use App\Filament\Resources\UserCourses\UserCourseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserCourse extends CreateRecord
{
    protected static string $resource = UserCourseResource::class;

    /** El cliente pidió quitar "Create & create another" del panel. */
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->formId('form'),
            $this->getCancelFormAction()->formId('form'),
        ];
    }
}
