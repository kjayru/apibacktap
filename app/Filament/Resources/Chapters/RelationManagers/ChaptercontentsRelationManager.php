<?php

namespace App\Filament\Resources\Chapters\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ChaptercontentsRelationManager extends RelationManager
{
    protected static string $relationship = 'chaptercontents';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->maxLength(255),
                FileUpload::make('video')
                    ->disk('public')
                    ->directory('video')
                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                    ->required(),
                FileUpload::make('poster')
                    ->disk('public')
                    ->directory('poster')
                    ->image(),
                FileUpload::make('audio')
                    ->disk('public')
                    ->directory('audio')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg']),
                RichEditor::make('contenido')
                    ->label('Content')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('order')
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->columns([
                TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('titulo')
                    ->searchable(),
                ImageColumn::make('poster')
                    ->label('Video')
                    ->disk('public')
                    ->height(60)
                    ->url(fn ($record) => $record->video ? Storage::disk('public')->url($record->video) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('audio')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
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
}
