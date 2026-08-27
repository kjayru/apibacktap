<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EventInfolist
{
    /** Los mismos datos que deja editar el formulario, y la descripción sin etiquetas (#1402). */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')
                    ->label('Title'),
                TextEntry::make('price')
                    ->label('Price')
                    ->money('USD'),
                TextEntry::make('duration')
                    ->label('Duration hours')
                    ->numeric(),
                TextEntry::make('start_date')
                    ->label('Event date')
                    ->date('M d, Y'),
                TextEntry::make('start_hour')
                    ->label('Start time')
                    ->time(),
                TextEntry::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->label('Description')
                    ->html()
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
