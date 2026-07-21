<?php

namespace App\Filament\Resources\Industries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class IndustryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('titulo'),
                TextEntry::make('slug'),
                TextEntry::make('banner')
                    ->placeholder('-'),
                TextEntry::make('card')
                    ->placeholder('-'),
                TextEntry::make('contenido')
                    ->columnSpanFull(),
                TextEntry::make('orden')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('category_id')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
