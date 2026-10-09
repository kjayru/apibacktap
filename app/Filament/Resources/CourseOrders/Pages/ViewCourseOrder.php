<?php

namespace App\Filament\Resources\CourseOrders\Pages;

use App\Filament\Resources\CourseOrders\CourseOrderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewCourseOrder extends ViewRecord
{
    protected static string $resource = CourseOrderResource::class;

    /** Una orden pagada se consulta, no se edita, igual que en el listado. */
    protected function getHeaderActions(): array
    {
        return [
            // Versión para imprimir, en una pestaña aparte (#1735).
            Action::make('print')
                ->label('Print')
                ->icon(Heroicon::OutlinedPrinter)
                ->url(fn (): string => route('admin.print.order', ['courseOrder' => $this->getRecord()]))
                ->openUrlInNewTab(),
        ];
    }
}
