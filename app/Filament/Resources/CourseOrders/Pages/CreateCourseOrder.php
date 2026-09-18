<?php

namespace App\Filament\Resources\CourseOrders\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\CourseOrders\CourseOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseOrder extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = CourseOrderResource::class;
}
