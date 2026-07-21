<?php

namespace App\Filament\Resources\CourseOrders\Pages;

use App\Filament\Resources\CourseOrders\CourseOrderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCourseOrder extends ViewRecord
{
    protected static string $resource = CourseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
