<?php

namespace App\Filament\Resources\Forms8850\Tables;

use App\Models\Form;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class Forms8850Table
{
    public static function configure(Table $table): Table
    {
        return $table
            // El primero de la lista debe ser el último envío recibido.
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('yourname')
                    ->label('Name')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('socialnumber')
                    ->label('SSN')
                    ->searchable(),
                TextColumn::make('birthday')
                    ->label('Birthday')
                    ->placeholder('-'),
                TextColumn::make('address')
                    ->label('Address')
                    ->wrap()
                    ->placeholder('-'),
                TextColumn::make('citystate')
                    ->label('City / State')
                    ->placeholder('-'),
                TextColumn::make('telephone')
                    ->label('Phone')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('checked_statements')
                    ->label('Statements')
                    ->badge()
                    ->state(fn (Form $record): array => $record->checked_statements)
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
