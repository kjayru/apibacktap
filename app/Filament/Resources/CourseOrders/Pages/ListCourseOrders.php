<?php

namespace App\Filament\Resources\CourseOrders\Pages;

use App\Filament\Resources\CourseOrders\CourseOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListCourseOrders extends ListRecords
{
    protected static string $resource = CourseOrderResource::class;

    protected function getHeaderActions(): array
    {
        // Las órdenes las genera el checkout, no se crean a mano.
        return [];
    }
}
