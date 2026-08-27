<?php

namespace App\Filament\Resources\UsersTaking;

use App\Filament\Resources\UsersTaking\Pages\EditUsersTaking;
use App\Filament\Resources\UsersTaking\Pages\UserCourses;
use App\Filament\Resources\Users\Schemas\UserProfileForm;
use App\Filament\Resources\UsersTaking\Pages\ListUsersTaking;
use App\Filament\Resources\UsersTaking\Tables\UsersTakingTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class UsersTakingResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    // Nombre de la sección tal y como lo pide el tablero (#1625).
    protected static ?string $navigationLabel = 'User who is taking or has taken course(s)';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'User who is taking or has taken course(s)';

    protected static string|UnitEnum|null $navigationGroup = 'Courses';

    protected static ?string $slug = 'users-taking';

    protected static ?int $navigationSort = 50;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->role('usuario')
            ->whereHas('userCourses');
    }

    public static function form(Schema $schema): Schema
    {
        return UserProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTakingTable::configure($table);
    }

    public static function getRelations(): array
    {
        // El curso realizado no se toca desde aquí: ni reiniciarlo ni borrarlo (#1638).
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsersTaking::route('/'),
            'courses' => UserCourses::route('/{record}/courses'),
            'edit' => EditUsersTaking::route('/{record}/edit'),
        ];
    }
}
