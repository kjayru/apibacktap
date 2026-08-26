<?php

namespace App\Filament\Resources\Contacts\Schemas;

use App\Filament\Resources\Contacts\Tables\ContactsTable;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContactInfolist
{
    /**
     * De las dos fechas sólo interesa cuándo se envió el mensaje, y se titula "Date"
     * (#1461, #1462). El origen se traduce igual que en el listado (#1458, #1460).
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Name'),
                TextEntry::make('phone')
                    ->label('Phone')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('origen')
                    ->label('Origin')
                    ->formatStateUsing(fn (?string $state): string => ContactsTable::originLabel($state)),
                TextEntry::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y H:i')
                    ->placeholder('-'),
                TextEntry::make('message')
                    ->label('Message')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
