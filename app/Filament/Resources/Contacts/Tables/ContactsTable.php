<?php

namespace App\Filament\Resources\Contacts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactsTable
{
    /**
     * Un contacto es un mensaje que ya llegó: se consulta, no se edita (#1459). Se añade
     * la fecha de envío (#1457) y el origen se muestra en limpio (#1458, #1460): "api-home"
     * y "home" son el mismo formulario de la portada, sólo cambia por dónde entró.
     */
    public static function configure(Table $table): Table
    {
        return $table
            // El primero de la lista debe ser el último registro creado.
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('origen')
                    ->label('Origin')
                    ->formatStateUsing(fn (?string $state): string => static::originLabel($state)),
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

    public static function originLabel(?string $state): string
    {
        return match ($state) {
            'home', 'api-home' => 'Home',
            'contact', 'api-contact' => 'Contact',
            default => (string) $state,
        };
    }
}
