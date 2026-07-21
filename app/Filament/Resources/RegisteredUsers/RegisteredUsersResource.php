<?php

namespace App\Filament\Resources\RegisteredUsers;

use App\Filament\Resources\RegisteredUsers\Pages\EditRegisteredUser;
use App\Filament\Resources\RegisteredUsers\Pages\ListRegisteredUsers;
use App\Filament\Resources\RegisteredUsers\Pages\ViewRegisteredUser;
use App\Filament\Resources\Users\RelationManagers\UserCoursesRelationManager;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class RegisteredUsersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?string $navigationLabel = 'registered users only';

    protected static ?string $modelLabel = 'Registered user';

    protected static ?string $pluralModelLabel = 'registered users only';

    protected static string|UnitEnum|null $navigationGroup = 'Courses';

    protected static ?string $slug = 'registered-users-only';

    protected static ?int $navigationSort = 60;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->role('usuario')
            ->whereDoesntHave('userCourses');
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            UserCoursesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegisteredUsers::route('/'),
            'view' => ViewRegisteredUser::route('/{record}'),
            'edit' => EditRegisteredUser::route('/{record}/edit'),
        ];
    }
}
