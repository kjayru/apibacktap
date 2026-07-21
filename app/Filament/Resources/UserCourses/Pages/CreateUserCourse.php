<?php

namespace App\Filament\Resources\UserCourses\Pages;

use App\Filament\Resources\UserCourses\UserCourseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserCourse extends CreateRecord
{
    protected static string $resource = UserCourseResource::class;
}
