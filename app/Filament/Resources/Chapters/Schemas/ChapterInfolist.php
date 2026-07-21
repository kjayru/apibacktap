<?php

namespace App\Filament\Resources\Chapters\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ChapterInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('contenido')
                    ->placeholder('-'),
                TextEntry::make('course_id')
                    ->numeric(),
                TextEntry::make('slug')
                    ->placeholder('-'),
                TextEntry::make('video')
                    ->placeholder('-'),
                TextEntry::make('reading')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('audio')
                    ->placeholder('-'),
                TextEntry::make('quiz')
                    ->numeric()
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
