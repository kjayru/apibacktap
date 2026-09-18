<?php

namespace App\Filament\Resources\UserCourses\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\UserCourses\UserCourseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserCourse extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = UserCourseResource::class;
}
