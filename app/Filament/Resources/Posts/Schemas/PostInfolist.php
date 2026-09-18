<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PostInfolist
{
    /**
     * Etiquetas en inglés (#1499), miniaturas en lugar del nombre del archivo (#1496) y
     * el contenido renderizado, no con las etiquetas a la vista (#1500).
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('titulo')
                    ->label('Title'),
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
                TextEntry::make('resumen')
                    ->label('Resume')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('contenido')
                    ->label('Content')
                    ->html()
                    ->columnSpanFull(),
            ]);
    }
}
