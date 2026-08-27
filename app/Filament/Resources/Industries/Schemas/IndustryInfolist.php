<?php

namespace App\Filament\Resources\Industries\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class IndustryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('titulo')->label('Title'),
                TextEntry::make('Category.name')->label('Category')->placeholder('-'),
                // Miniaturas en vez del nombre del archivo.
                ImageEntry::make('card')->label('Card')->disk('public'),
                ImageEntry::make('banner')->label('Banner')->disk('public'),
                // El contenido llega como HTML: se muestra formateado, no con las etiquetas a la vista.
                TextEntry::make('contenido')
                    ->label('Content')
                    ->html()
                    ->columnSpanFull(),
            ]);
    }
}
