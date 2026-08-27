<?php

namespace App\Filament\Resources\Chaptercontents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ChaptercontentsTable
{
    /**
     * Las columnas del listado anterior (#1582, #1583): título, un extracto del contenido,
     * el vídeo y el audio reproducibles ahí mismo (#1584) y la fecha.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns(static::columns())
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /** @return array<int, mixed> */
    public static function columns(): array
    {
        return [
            TextColumn::make('titulo')
                ->label('Title')
                ->wrap()
                ->searchable(),
            TextColumn::make('contenido')
                ->label('Excerpt')
                ->wrap()
                ->state(fn ($record): string => Str::limit(strip_tags((string) $record->contenido), 60))
                ->placeholder('-'),
            ViewColumn::make('video')
                ->label('Video')
                ->view('filament.tables.columns.media-preview', ['type' => 'video']),
            ViewColumn::make('audio')
                ->label('Audio')
                ->view('filament.tables.columns.media-preview', ['type' => 'audio']),
            TextColumn::make('created_at')
                ->label('Date')
                ->dateTime('M d, Y')
                ->sortable(),
        ];
    }
}
