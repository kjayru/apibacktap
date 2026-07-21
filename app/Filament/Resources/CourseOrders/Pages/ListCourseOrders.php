<?php

namespace App\Filament\Resources\CourseOrders\Pages;

use App\Filament\Resources\CourseOrders\CourseOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCourseOrders extends ListRecords
{
    protected static string $resource = CourseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
