<?php

namespace App\Filament\Resources\Chaptercontents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ChaptercontentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('titulo')
                    ->placeholder('-'),
                TextEntry::make('slug')
                    ->placeholder('-'),
                TextEntry::make('video'),
                TextEntry::make('poster')
                    ->placeholder('-'),
                TextEntry::make('contenido')
                    ->columnSpanFull(),
                TextEntry::make('chapter_id')
                    ->numeric(),
                TextEntry::make('audio')
                    ->placeholder('-'),
                TextEntry::make('order')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
