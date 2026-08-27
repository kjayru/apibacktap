<?php

namespace App\Filament\Resources\CourseOrders\Pages;

use App\Filament\Resources\CourseOrders\CourseOrderResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCourseOrder extends ViewRecord
{
    protected static string $resource = CourseOrderResource::class;

    /** Una orden pagada se consulta, no se edita, igual que en el listado. */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
