<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('titulo'),
                TextEntry::make('subtitulo')
                    ->placeholder('-'),
                TextEntry::make('slug'),
                TextEntry::make('banner')
                    ->placeholder('-'),
                TextEntry::make('video')
                    ->placeholder('-'),
                TextEntry::make('resumen')
                    ->placeholder('-'),
                TextEntry::make('contenido')
                    ->columnSpanFull(),
                TextEntry::make('precio')
                    ->numeric(),
                TextEntry::make('disponible')
                    ->placeholder('-'),
                TextEntry::make('capitulos')
                    ->placeholder('-'),
                TextEntry::make('audio')
                    ->placeholder('-'),
                TextEntry::make('nivel')
                    ->placeholder('-'),
                TextEntry::make('language')
                    ->placeholder('-'),
                TextEntry::make('responsable')
                    ->placeholder('-'),
                TextEntry::make('tiempovalido')
                    ->placeholder('-'),
                TextEntry::make('certification_id')
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
