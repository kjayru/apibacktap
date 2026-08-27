<?php

namespace App\Filament\Resources\UsersTaking\Tables;

use App\Filament\Resources\UsersTaking\UsersTakingResource;
use App\Models\User;
use App\Models\UserSign;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTakingTable
{
    /**
     * Las mismas columnas que el listado de producción (#1626, #1627): los datos salen
     * del perfil, no de la cuenta. "Courses" y "Enrollment" son columnas con su botón
     * dentro (#1629), no acciones al final de la fila, para que se vean de un vistazo.
     * Sin "view" (#1630): lo que interesa de un usuario es su perfil y sus cursos.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with('profile')
                ->withCount([
                    'userCourses as completed_courses_count' => fn (Builder $q) => $q->where('finalizado', 1),
                ]))
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->state(fn (User $record): string => static::fullName($record))
                    ->searchable(),
                TextColumn::make('profile.birthday')
                    ->label('Birthday')
                    ->placeholder('-'),
                TextColumn::make('profile.ssn')
                    ->label('SSN')
                    ->placeholder('-'),
                TextColumn::make('profile.drivernumber')
                    ->label('License')
                    ->placeholder('-'),
                TextColumn::make('profile.zipcode')
                    ->label('Zip code')
                    ->placeholder('-'),
                TextColumn::make('courses')
                    ->label('Courses')
                    ->badge()
                    ->color('warning')
                    ->state('Courses')
                    ->url(fn (User $record): string => UsersTakingResource::getUrl('courses', ['record' => $record])),
                TextColumn::make('enrollment')
                    ->label('Enrollment')
                    ->badge()
                    ->color(fn (User $record): string => static::hasSign($record) ? 'warning' : 'gray')
                    ->state(fn (User $record): string => static::hasSign($record) ? 'Enrollment' : '-')
                    ->action(static::enrollmentAction()),
                TextColumn::make('completed_courses_count')
                    ->label('Completed courses')
                    ->badge()
                    ->color('success'),
                TextColumn::make('profile.created_at')
                    ->label('Date')
                    ->dateTime('M j, Y H:i:s')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /** Nombre completo tal y como lo componía el listado anterior. */
    public static function fullName(User $record): string
    {
        return trim(implode(' ', array_filter([
            $record->name,
            $record->profile?->lastname,
            $record->profile?->middlename,
        ])));
    }

    public static function hasSign(User $record): bool
    {
        return UserSign::where('user_id', $record->getKey())->exists();
    }

    /** El documento que el admin anterior servía en users/enroll/{id}. */
    public static function enrollmentAction(): Action
    {
        return Action::make('enrollment')
            ->modalHeading('User enrollment')
            ->modalWidth(Width::FourExtraLarge)
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->modalContent(fn (User $record) => view('filament.user-courses.enrollment', [
                'sign' => UserSign::where('user_id', $record->getKey())->latest('id')->first(),
            ]));
    }
}
