<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    /**
     * Card y banner se ven como miniatura y no como nombre de archivo (#1469), y
     * "parent id" desaparece de la ficha (#1470).
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Name'),
                TextEntry::make('slug')
                    ->label('Slug'),
                TextEntry::make('orden')
                    ->label('Order')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->placeholder('-'),
                ImageEntry::make('card')
                    ->label('Card')
                    ->disk('public')
                    ->height(160)
                    ->placeholder('-'),
                ImageEntry::make('banner')
                    ->label('Banner')
                    ->disk('public')
                    ->height(160)
                    ->placeholder('-'),
            ]);
    }
}
