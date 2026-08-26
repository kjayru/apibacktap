<?php

namespace App\Filament\Resources\RegisteredUsers\Tables;

use App\Filament\Resources\UsersTaking\Tables\UsersTakingTable;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RegisteredUsersTable
{
    /**
     * Estos usuarios se registraron pero no han comprado curso, así que el listado se
     * queda en los datos del perfil (#1641). Sin "view" (#1642) y con borrado (#1643),
     * igual que en el admin anterior.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('profile'))
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->state(fn (User $record): string => UsersTakingTable::fullName($record))
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
}
