<?php

namespace App\Filament\Resources\ChapterQuizOptions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ChapterQuizOptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('chapter_quiz_id')
                    ->numeric(),
                TextEntry::make('option')
                    ->columnSpanFull(),
                TextEntry::make('estado')
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
