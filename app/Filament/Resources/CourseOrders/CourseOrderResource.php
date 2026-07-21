<?php

namespace App\Filament\Resources\CourseOrders;

use App\Filament\Resources\CourseOrders\Pages\CreateCourseOrder;
use App\Filament\Resources\CourseOrders\Pages\EditCourseOrder;
use App\Filament\Resources\CourseOrders\Pages\ListCourseOrders;
use App\Filament\Resources\CourseOrders\Pages\ViewCourseOrder;
use App\Filament\Resources\CourseOrders\Schemas\CourseOrderForm;
use App\Filament\Resources\CourseOrders\Schemas\CourseOrderInfolist;
use App\Filament\Resources\CourseOrders\Tables\CourseOrdersTable;
use App\Models\CourseOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CourseOrderResource extends Resource
{
    protected static ?string $model = CourseOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $navigationLabel = 'Orders';

    protected static ?string $modelLabel = 'Order';

    protected static ?string $pluralModelLabel = 'Orders';

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return CourseOrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CourseOrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseOrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourseOrders::route('/'),
            'create' => CreateCourseOrder::route('/create'),
            'view' => ViewCourseOrder::route('/{record}'),
            'edit' => EditCourseOrder::route('/{record}/edit'),
        ];
    }
}
