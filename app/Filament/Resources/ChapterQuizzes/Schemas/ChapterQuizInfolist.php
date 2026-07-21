<?php

namespace App\Filament\Resources\ChapterQuizzes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ChapterQuizInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('chapter_id')
                    ->numeric(),
                TextEntry::make('quiz_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('question')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
