<?php

namespace App\Filament\Resources\UserCourses;

use App\Filament\Resources\UserCourses\Pages\CreateUserCourse;
use App\Filament\Resources\UserCourses\Pages\EditUserCourse;
use App\Filament\Resources\UserCourses\Pages\ListUserCourses;
use App\Filament\Resources\UserCourses\Pages\ViewUserCourse;
use App\Filament\Resources\UserCourses\Schemas\UserCourseForm;
use App\Filament\Resources\UserCourses\Schemas\UserCourseInfolist;
use App\Filament\Resources\UserCourses\Tables\UserCoursesTable;
use App\Models\UserCourse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserCourseResource extends Resource
{
    protected static ?string $model = UserCourse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return UserCourseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserCourseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserCoursesTable::configure($table);
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
            'index' => ListUserCourses::route('/'),
            'create' => CreateUserCourse::route('/create'),
            'view' => ViewUserCourse::route('/{record}'),
            'edit' => EditUserCourse::route('/{record}/edit'),
        ];
    }
}
