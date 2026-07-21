<?php

namespace App\Filament\Resources\CourseOrders\Pages;

use App\Filament\Resources\CourseOrders\CourseOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCourseOrder extends EditRecord
{
    protected static string $resource = CourseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
