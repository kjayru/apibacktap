<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Contacts\ContactResource;
use App\Filament\Resources\Events\EventResource;
use App\Models\Contact;
use App\Models\CourseOrder;
use App\Models\Event;
use App\Models\Information;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LegacyOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Dashboard';

    protected int | array | null $columns = [
        'default' => 1,
        'md' => 2,
        'xl' => 4,
    ];

    protected function getStats(): array
    {
        return [
            Stat::make('Trainings', Event::query()->count())
                ->description('View')
                ->descriptionIcon(Heroicon::ArrowRightCircle)
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('success')
                ->url(EventResource::getUrl()),

            Stat::make('Orders', CourseOrder::query()->count())
                ->description('View')
                ->descriptionIcon(Heroicon::ArrowRightCircle)
                ->icon(Heroicon::OutlinedShoppingBag)
                ->color('warning'),

            Stat::make('Applicants', Information::query()->count())
                ->description('View')
                ->descriptionIcon(Heroicon::ArrowRightCircle)
                ->icon(Heroicon::OutlinedUserPlus)
                ->color('danger'),

            Stat::make('Contacts', Contact::query()->count())
                ->description('View')
                ->descriptionIcon(Heroicon::ArrowRightCircle)
                ->icon(Heroicon::OutlinedEnvelope)
                ->color('info')
                ->url(ContactResource::getUrl()),
        ];
    }
}
