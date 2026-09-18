<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\Courses\CourseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = CourseResource::class;
}
