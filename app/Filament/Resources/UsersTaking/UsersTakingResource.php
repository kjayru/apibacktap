<?php

namespace App\Filament\Resources\UsersTaking;

use App\Filament\Resources\UsersTaking\Pages\EditUsersTaking;
use App\Filament\Resources\UsersTaking\Pages\UserCourses;
use App\Filament\Resources\Users\RelationManagers\UserCoursesRelationManager;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Filament\Resources\UsersTaking\Pages\ListUsersTaking;
use App\Filament\Resources\UsersTaking\Pages\ViewUsersTaking;
use App\Models\User;
use App\Models\UserSign;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class UsersTakingResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Users taking';

    protected static ?string $modelLabel = 'User taking';

    protected static ?string $pluralModelLabel = 'Users taking';

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
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table)
            ->recordActions([
                Action::make('courses')
                    ->label('Courses')
                    ->icon('heroicon-o-academic-cap')
                    ->color('warning')
                    ->url(fn (User $record): string => static::getUrl('courses', ['record' => $record])),
                // El admin anterior servía este documento en users/enroll/{id}.
                Action::make('enrollment')
                    ->label('Enrollment')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->modalHeading('User enrollment')
                    ->modalWidth(Width::FourExtraLarge)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn (User $record) => view('filament.user-courses.enrollment', [
                        'sign' => UserSign::where('user_id', $record->getKey())->latest('id')->first(),
                    ]))
                    ->visible(fn (User $record): bool => UserSign::where('user_id', $record->getKey())->exists()),
                ViewAction::make(),
                EditAction::make(),
            ]);
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
            'index' => ListUsersTaking::route('/'),
            'courses' => UserCourses::route('/{record}/courses'),
            'view' => ViewUsersTaking::route('/{record}'),
            'edit' => EditUsersTaking::route('/{record}/edit'),
        ];
    }
}
