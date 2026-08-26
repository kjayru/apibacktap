<?php

namespace App\Filament\Resources\RegisteredUsers;

use App\Filament\Resources\RegisteredUsers\Pages\EditRegisteredUser;
use App\Filament\Resources\RegisteredUsers\Pages\ListRegisteredUsers;
use App\Filament\Resources\RegisteredUsers\Tables\RegisteredUsersTable;
use App\Filament\Resources\Users\Schemas\UserProfileForm;
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
        return UserProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RegisteredUsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        // Estos usuarios no tienen cursos, el bloque sobraba (#1644).
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegisteredUsers::route('/'),
            'edit' => EditRegisteredUser::route('/{record}/edit'),
        ];
    }
}
